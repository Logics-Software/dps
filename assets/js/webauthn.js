// WebAuthn helper functions
class WebAuthnHelper {
    // Check if WebAuthn is supported
    static isSupported() {
        return typeof window.PublicKeyCredential !== 'undefined';
    }

    // Convert base64url to ArrayBuffer
    static base64UrlToArrayBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const binaryString = window.atob(base64);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        return bytes.buffer;
    }

    // Convert ArrayBuffer to base64url
    static arrayBufferToBase64Url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        const base64 = window.btoa(binary);
        return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    // Convert credential to format for sending to server
    static credentialToJSON(credential) {
        if (credential instanceof Array) {
            return credential.map(c => WebAuthnHelper.credentialToJSON(c));
        }

        if (credential instanceof ArrayBuffer) {
            return WebAuthnHelper.arrayBufferToBase64Url(credential);
        }

        if (credential instanceof Object) {
            const obj = {};
            for (let key in credential) {
                obj[key] = WebAuthnHelper.credentialToJSON(credential[key]);
            }
            return obj;
        }

        return credential;
    }

    // Start registration process
    static async registerBiometric() {
        if (!WebAuthnHelper.isSupported()) {
            throw new Error('WebAuthn tidak didukung di browser ini. Pastikan menggunakan browser modern (Chrome, Firefox, Edge, Safari)');
        }

        try {
            // Get registration options from server
            const response = await fetch('/api/webauthn/registration/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include'
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Gagal memulai registrasi');
            }

            const options = data.options;

            // Convert challenge to ArrayBuffer
            options.challenge = WebAuthnHelper.base64UrlToArrayBuffer(options.challenge);
            options.user.id = WebAuthnHelper.base64UrlToArrayBuffer(options.user.id);

            // Convert allowCredentials if exists
            if (options.allowCredentials) {
                options.allowCredentials = options.allowCredentials.map(cred => ({
                    ...cred,
                    id: WebAuthnHelper.base64UrlToArrayBuffer(cred.id)
                }));
            }

            // Create credential
            const credential = await navigator.credentials.create({
                publicKey: options
            });

            // Convert credential to JSON for sending to server
            const publicKeyArrayBuffer = credential.response.getPublicKey ? credential.response.getPublicKey() : null;
            
            const credentialJSON = {
                id: credential.id,
                rawId: WebAuthnHelper.arrayBufferToBase64Url(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: WebAuthnHelper.arrayBufferToBase64Url(credential.response.clientDataJSON),
                    attestationObject: WebAuthnHelper.arrayBufferToBase64Url(credential.response.attestationObject)
                }
            };
            
            // Add publicKey if available
            if (publicKeyArrayBuffer) {
                credentialJSON.response.publicKey = WebAuthnHelper.arrayBufferToBase64Url(publicKeyArrayBuffer);
            }

            // Send to server
            const completeResponse = await fetch('/api/webauthn/registration/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    credential: credentialJSON
                })
            });

            const completeData = await completeResponse.json();
            
            if (!completeData.success) {
                throw new Error(completeData.error || 'Gagal menyelesaikan registrasi');
            }

            return completeData;
        } catch (error) {
            console.error('WebAuthn registration error:', error);
            throw error;
        }
    }

    // Start authentication process
    static async authenticateBiometric(username) {
        if (!WebAuthnHelper.isSupported()) {
            throw new Error('WebAuthn tidak didukung di browser ini');
        }

        try {
            // Get authentication options from server
            const response = await fetch('/api/webauthn/authentication/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ username })
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Gagal memulai autentikasi');
            }

            const options = data.options;

            // Convert challenge to ArrayBuffer
            options.challenge = WebAuthnHelper.base64UrlToArrayBuffer(options.challenge);

            // Convert allowCredentials
            if (options.allowCredentials && options.allowCredentials.length > 0) {
                options.allowCredentials = options.allowCredentials.map(cred => ({
                    ...cred,
                    id: WebAuthnHelper.base64UrlToArrayBuffer(cred.id)
                }));
            }

            // Get credential
            const credential = await navigator.credentials.get({
                publicKey: options
            });

            // Convert credential to JSON
            const credentialJSON = {
                id: credential.id,
                rawId: WebAuthnHelper.arrayBufferToBase64Url(credential.rawId),
                type: credential.type,
                response: {
                    authenticatorData: WebAuthnHelper.arrayBufferToBase64Url(credential.response.authenticatorData),
                    clientDataJSON: WebAuthnHelper.arrayBufferToBase64Url(credential.response.clientDataJSON),
                    signature: WebAuthnHelper.arrayBufferToBase64Url(credential.response.signature),
                    userHandle: credential.response.userHandle ? WebAuthnHelper.arrayBufferToBase64Url(credential.response.userHandle) : null
                }
            };

            // Send to server
            const completeResponse = await fetch('/api/webauthn/authentication/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    credential: credentialJSON
                })
            });

            const completeData = await completeResponse.json();
            
            if (!completeData.success) {
                throw new Error(completeData.error || 'Autentikasi gagal');
            }

            return completeData;
        } catch (error) {
            console.error('WebAuthn authentication error:', error);
            throw error;
        }
    }

    // Check if user has biometric credentials
    static async hasCredentials(username) {
        try {
            const response = await fetch('/api/webauthn/authentication/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({ username })
            });

            if (!response.ok) {
                return false;
            }

            const data = await response.json();
            
            if (data.success && data.options) {
                const allowCredentials = data.options.allowCredentials;
                if (allowCredentials && Array.isArray(allowCredentials)) {
                    return allowCredentials.length > 0;
                }
            }
            
            return false;
        } catch (error) {
            console.error('Error checking credentials:', error);
            return false;
        }
    }

    // List user credentials
    static async listCredentials() {
        const response = await fetch('/api/webauthn/credentials', {
            method: 'GET',
            credentials: 'include'
        });

        const data = await response.json();
        return data.success ? data.credentials : [];
    }

    // Delete credential
    static async deleteCredential(credentialId) {
        const response = await fetch('/api/webauthn/credentials/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({ credential_id: credentialId })
        });

        const data = await response.json();
        return data.success;
    }
}
