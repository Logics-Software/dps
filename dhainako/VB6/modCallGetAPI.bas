Attribute VB_Name = "modCallGetAPI"
'---------------------------------------------------------------------------------
' Material Untuk HIT API (GET/POST/UPDATE/DELETE/PATCH)
'---------------------------------------------------------------------------------

Option Explicit

' ============================================
' Fungsi Utama untuk Call API
' ============================================
Public Function CallAPI(method As String, url As String, Optional data As String = "") As String
    Dim http As Object
    Dim response As String
    Dim status As Long
    
    On Error GoTo ErrorHandler
    
    Set http = CreateObject("MSXML2.XMLHTTP")
    
    Select Case UCase(method)
        Case "GET"
            http.Open "GET", url, False
            http.send
        Case "POST"
            http.Open "POST", url, False
            If InStr(data, "{") > 0 Then
                ' JSON
                http.setRequestHeader "Content-Type", "application/json"
            Else
                ' Form URL Encoded
                http.setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
            End If
            http.send data
        Case "PUT"
            http.Open "PUT", url, False
            http.setRequestHeader "Content-Type", "application/json"
            http.send data
        Case "PATCH"
            http.Open "PATCH", url, False
            http.setRequestHeader "Content-Type", "application/json"
            http.send data
        Case "DELETE"
            http.Open "DELETE", url, False
            http.send
    End Select
    
    status = http.status
    response = http.responseText
    
    If status >= 200 And status < 300 Then
        CallAPI = response
    Else
        CallAPI = "ERROR:" & status & "|" & response
    End If
    
    Set http = Nothing
    Exit Function
    
ErrorHandler:
    CallAPI = "ERROR: " & Err.Description
    Set http = Nothing
End Function

' ============================================
' URL Encoding untuk karakter spesial
' ============================================
Public Function URLEncode(text As String) As String
    Dim i As Long
    Dim char As String
    Dim result As String
    Dim ascii As Integer
    
    result = ""
    For i = 1 To Len(text)
        char = Mid(text, i, 1)
        ascii = Asc(char)
        
        Select Case char
            Case " "
                result = result & "+"
            Case "A" To "Z", "a" To "z", "0" To "9", "-", "_", ".", "~"
                result = result & char
            Case Else
                If ascii < 128 Then
                    result = result & "%" & Right("0" & Hex(ascii), 2)
                Else
                    ' UTF-8 encoding untuk karakter non-ASCII
                    result = result & "%" & Right("0" & Hex(ascii \ 16), 1) & Right("0" & Hex(ascii Mod 16), 1)
                End If
        End Select
    Next i
    
    URLEncode = result
End Function

' ============================================
' URL Decoding untuk decode data yang di-encode
' ============================================
Public Function URLDecode(encodedText As String) As String
    Dim i As Long
    Dim char As String
    Dim result As String
    Dim hexValue As String
    Dim asciiValue As Integer
    
    result = ""
    i = 1
    
    Do While i <= Len(encodedText)
        char = Mid(encodedText, i, 1)
        
        If char = "+" Then
            ' Plus sign = space
            result = result & " "
            i = i + 1
        ElseIf char = "%" And i + 2 <= Len(encodedText) Then
            ' Percent encoded character
            hexValue = Mid(encodedText, i + 1, 2)
            On Error Resume Next
            asciiValue = CInt("&H" & hexValue)
            If Err.Number = 0 Then
                result = result & Chr(asciiValue)
            Else
                ' Jika gagal decode, biarkan as is
                result = result & char
            End If
            On Error GoTo 0
            i = i + 3
        Else
            ' Regular character
            result = result & char
            i = i + 1
        End If
    Loop
    
    URLDecode = result
End Function

' ============================================
' Parse JSON Value
' ============================================
Public Function ParseJSONValue(jsonString As String, key As String, Optional shouldDecode As Boolean = False) As String
    Dim startPos As Long
    Dim endPos As Long
    Dim searchKey As String
    Dim char As String
    Dim value As String
    
    searchKey = """" & key & """:"
    startPos = InStr(1, jsonString, searchKey, vbTextCompare)
    
    If startPos > 0 Then
        startPos = startPos + Len(searchKey)
        
        ' Skip whitespace
        Do While startPos <= Len(jsonString) And (Mid(jsonString, startPos, 1) = " " Or Mid(jsonString, startPos, 1) = vbTab)
            startPos = startPos + 1
        Loop
        
        char = Mid(jsonString, startPos, 1)
        
        If char = """" Then
            ' String value - handle JSON escape sequences
            startPos = startPos + 1
            endPos = startPos
            Do While endPos <= Len(jsonString)
                char = Mid(jsonString, endPos, 1)
                If char = "\" Then
                    ' Skip escape character and next character
                    endPos = endPos + 2
                ElseIf char = """" Then
                    ' Found end quote
                    Exit Do
                Else
                    endPos = endPos + 1
                End If
            Loop
            If endPos > Len(jsonString) Then endPos = Len(jsonString) + 1
        ElseIf char = "{" Or char = "[" Then
            ' Object or Array - find matching closing bracket
            Dim depth As Integer
            Dim bracket As String
            bracket = IIf(char = "{", "}", "]")
            depth = 1
            endPos = startPos + 1
            Do While endPos <= Len(jsonString) And depth > 0
                If Mid(jsonString, endPos, 1) = char Then
                    depth = depth + 1
                ElseIf Mid(jsonString, endPos, 1) = bracket Then
                    depth = depth - 1
                End If
                endPos = endPos + 1
            Loop
        Else
            ' Number, boolean, or null
            endPos = startPos
            Do While endPos <= Len(jsonString)
                char = Mid(jsonString, endPos, 1)
                If (char >= "0" And char <= "9") Or char = "." Or char = "-" Or _
                   char = "e" Or char = "E" Or char = "+" Or _
                   (char >= "a" And char <= "z") Or (char >= "A" And char <= "Z") Then
                    endPos = endPos + 1
                Else
                    Exit Do
                End If
            Loop
        End If
        
        value = Mid(jsonString, startPos, endPos - startPos)
        
        ' Handle JSON escape sequences in string values
        If Mid(jsonString, startPos - 1, 1) = """" Then
            ' Remove surrounding quotes if present
            If Left(value, 1) <> """" Then
                ' Value doesn't have quote at start, might be escaped
                value = Replace(value, "\""", """")
                value = Replace(value, "\\", "\")
                value = Replace(value, "\/", "/")
                value = Replace(value, "\b", Chr(8))
                value = Replace(value, "\f", Chr(12))
                value = Replace(value, "\n", vbCrLf)
                value = Replace(value, "\r", vbCr)
                value = Replace(value, "\t", vbTab)
            End If
        End If
        
        ' Apply URLDecode if requested (usually not needed for JSON)
        If shouldDecode Then
            ParseJSONValue = URLDecode(value)
        Else
            ParseJSONValue = value
        End If
    Else
        ParseJSONValue = ""
    End If
End Function


' ============================================
' Check API Response Success
' ============================================
Public Function IsAPISuccess(response As String) As Boolean
    Dim success As String
    success = ParseJSONValue(response, "success")
    IsAPISuccess = (LCase(success) = "true")
End Function

' ============================================
' Get API Error Message
' ============================================
Public Function GetAPIErrorMessage(response As String) As String
    Dim message As String
    message = ParseJSONValue(response, "message")
    If message = "" Then
        GetAPIErrorMessage = "Unknown error"
    Else
        GetAPIErrorMessage = message
    End If
End Function

