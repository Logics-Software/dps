Attribute VB_Name = "modCustomerAPI"
'---------------------------------------------------------------
' Module untuk API Mastercustomer
'---------------------------------------------------------------

Option Explicit

' Base URL API
Public Const API_BASE_URL As String = "http://localhost:8000/api/mastercustomer"

' ============================================
' MASTER CUSTOMER API WRAPPERS
' ============================================
Public Function GetAllMastercustomer(Optional page As Long = 1, Optional perPage As Long = 100, _
                                     Optional search As String = "", Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL & params
    GetAllMastercustomer = CallAPI("GET", url)
End Function

Public Function GetMastercustomerById(id As Long) As String
    Dim url As String
    url = API_BASE_URL & "?id=" & id
    GetMastercustomerById = CallAPI("GET", url)
End Function

Public Function GetMastercustomerByKode(kodeCustomer As String) As String
    Dim url As String
    url = API_BASE_URL & "?kodecustomer=" & URLEncode(kodeCustomer)
    GetMastercustomerByKode = CallAPI("GET", url)
End Function

Public Function CreateMastercustomer(kodeCustomer As String, namaCustomer As String, _
                                     Optional namabadanusaha As String = "", _
                                     Optional alamatcustomer As String = "", _
                                     Optional kotacustomer As String = "", _
                                     Optional notelepon As String = "", _
                                     Optional kontakperson As String = "", _
                                     Optional statuspkp As String = "nonpkp", _
                                     Optional npwp As String = "", _
                                     Optional namawp As String = "", _
                                     Optional alamatwp As String = "", _
                                     Optional namaapoteker As String = "", _
                                     Optional nosipa As String = "", _
                                     Optional tanggaledsipa As String = "", _
                                     Optional noijinusaha As String = "", _
                                     Optional tanggaledijinusaha As String = "", _
                                     Optional nocdob As String = "", _
                                     Optional tanggaledcdob As String = "", _
                                     Optional latitude As String = "", _
                                     Optional longitude As String = "", _
                                     Optional userid As String = "", _
                                     Optional status As String = "baru") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    ' Always send required fields
    postData = "kodecustomer=" & URLEncode(kodeCustomer) & _
               "&namacustomer=" & URLEncode(namaCustomer) & _
               "&statuspkp=" & URLEncode(statuspkp) & _
               "&status=" & URLEncode(status)
    
    ' Send all optional fields (including empty ones) so API can normalize them to null
    postData = postData & "&namabadanusaha=" & URLEncode(namabadanusaha)
    postData = postData & "&alamatcustomer=" & URLEncode(alamatcustomer)
    postData = postData & "&kotacustomer=" & URLEncode(kotacustomer)
    postData = postData & "&notelepon=" & URLEncode(notelepon)
    postData = postData & "&kontakperson=" & URLEncode(kontakperson)
    postData = postData & "&npwp=" & URLEncode(npwp)
    postData = postData & "&namawp=" & URLEncode(namawp)
    postData = postData & "&alamatwp=" & URLEncode(alamatwp)
    postData = postData & "&namaapoteker=" & URLEncode(namaapoteker)
    postData = postData & "&nosipa=" & URLEncode(nosipa)
    postData = postData & "&tanggaledsipa=" & URLEncode(tanggaledsipa)
    postData = postData & "&noijinusaha=" & URLEncode(noijinusaha)
    postData = postData & "&tanggaledijinusaha=" & URLEncode(tanggaledijinusaha)
    postData = postData & "&nocdob=" & URLEncode(nocdob)
    postData = postData & "&tanggaledcdob=" & URLEncode(tanggaledcdob)
    postData = postData & "&latitude=" & URLEncode(latitude)
    postData = postData & "&longitude=" & URLEncode(longitude)
    postData = postData & "&userid=" & URLEncode(userid)
    
    CreateMastercustomer = CallAPI("POST", url, postData)
End Function

Public Function UpdateMastercustomer(id As Long, _
                                     Optional kodeCustomer As String = "", _
                                     Optional namaCustomer As String = "", _
                                     Optional namabadanusaha As String = "", _
                                     Optional alamatcustomer As String = "", _
                                     Optional kotacustomer As String = "", _
                                     Optional notelepon As String = "", _
                                     Optional kontakperson As String = "", _
                                     Optional statuspkp As String = "", _
                                     Optional npwp As String = "", _
                                     Optional namawp As String = "", _
                                     Optional alamatwp As String = "", _
                                     Optional namaapoteker As String = "", _
                                     Optional nosipa As String = "", _
                                     Optional tanggaledsipa As String = "", _
                                     Optional noijinusaha As String = "", _
                                     Optional tanggaledijinusaha As String = "", _
                                     Optional nocdob As String = "", _
                                     Optional tanggaledcdob As String = "", _
                                     Optional latitude As String = "", _
                                     Optional longitude As String = "", _
                                     Optional userid As String = "", _
                                     Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=PUT&id=" & id
    
    ' Send all fields (including empty ones) so API can normalize empty strings to null
    ' This allows clearing fields that previously had values
    If kodeCustomer <> "" Then postData = postData & "&kodecustomer=" & URLEncode(kodeCustomer)
    If namaCustomer <> "" Then postData = postData & "&namacustomer=" & URLEncode(namaCustomer)
    postData = postData & "&namabadanusaha=" & URLEncode(namabadanusaha)
    postData = postData & "&alamatcustomer=" & URLEncode(alamatcustomer)
    postData = postData & "&kotacustomer=" & URLEncode(kotacustomer)
    postData = postData & "&notelepon=" & URLEncode(notelepon)
    postData = postData & "&kontakperson=" & URLEncode(kontakperson)
    If statuspkp <> "" Then postData = postData & "&statuspkp=" & URLEncode(statuspkp)
    postData = postData & "&npwp=" & URLEncode(npwp)
    postData = postData & "&namawp=" & URLEncode(namawp)
    postData = postData & "&alamatwp=" & URLEncode(alamatwp)
    postData = postData & "&namaapoteker=" & URLEncode(namaapoteker)
    postData = postData & "&nosipa=" & URLEncode(nosipa)
    postData = postData & "&tanggaledsipa=" & URLEncode(tanggaledsipa)
    postData = postData & "&noijinusaha=" & URLEncode(noijinusaha)
    postData = postData & "&tanggaledijinusaha=" & URLEncode(tanggaledijinusaha)
    postData = postData & "&nocdob=" & URLEncode(nocdob)
    postData = postData & "&tanggaledcdob=" & URLEncode(tanggaledcdob)
    postData = postData & "&latitude=" & URLEncode(latitude)
    postData = postData & "&longitude=" & URLEncode(longitude)
    postData = postData & "&userid=" & URLEncode(userid)
    If status <> "" Then postData = postData & "&status=" & URLEncode(status)
    
    UpdateMastercustomer = CallAPI("POST", url, postData)
End Function

Public Function UpdateMastercustomerStatusByKode(kodeCustomer As String, status As String) As String
    Dim url As String
    Dim postData As String

    url = API_BASE_URL
    postData = "action=update_status&kodecustomer=" & URLEncode(kodeCustomer) & _
               "&status=" & URLEncode(status)

    UpdateMastercustomerStatusByKode = CallAPI("POST", url, postData)
End Function

Public Function DeleteMastercustomerById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=DELETE&id=" & id
    
    DeleteMastercustomerById = CallAPI("POST", url, postData)
End Function

Public Function DeleteMastercustomerByKode(kodeCustomer As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=DELETE&kodecustomer=" & URLEncode(kodeCustomer)
    
    DeleteMastercustomerByKode = CallAPI("POST", url, postData)
End Function
