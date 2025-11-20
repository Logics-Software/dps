Attribute VB_Name = "ModuleAPI"
Option Explicit

' API Base URL - sesuaikan dengan URL server PHP Anda
Public Const API_BASE_URL As String = "http://localhost:8000/api/"

' ============================================
' PEMBELIANBARANG API FUNCTIONS
' ============================================

' Create Pembelianbarang
Public Function CreatePembelianbarang(nopembelian As String, tanggalpembelian As String, _
    namasupplier As String, kodebarang As String, jumlah As Double, harga As Double, _
    discount As Double, totalharga As Double) As String
    
    Dim jsonData As String
    Dim response As String
    
    jsonData = "{"
    jsonData = jsonData & """nopembelian"": """ & EscapeJSON(nopembelian) & ""","
    jsonData = jsonData & """tanggalpembelian"": """ & tanggalpembelian & ""","
    jsonData = jsonData & """namasupplier"": """ & EscapeJSON(namasupplier) & ""","
    jsonData = jsonData & """kodebarang"": """ & EscapeJSON(kodebarang) & ""","
    jsonData = jsonData & """jumlah"": " & jumlah & ","
    jsonData = jsonData & """harga"": " & harga & ","
    jsonData = jsonData & """discount"": " & discount & ","
    jsonData = jsonData & """totalharga"": " & totalharga
    jsonData = jsonData & "}"
    
    response = HttpPost(API_BASE_URL & "pembelianbarang", jsonData)
    CreatePembelianbarang = response
End Function

' Get Pembelianbarang by ID
Public Function GetPembelianbarangById(id As Long) As String
    Dim response As String
    response = HttpGet(API_BASE_URL & "pembelianbarang?id=" & id)
    GetPembelianbarangById = response
End Function

' Get Pembelianbarang by nopembelian
Public Function GetPembelianbarangByNo(nopembelian As String) As String
    Dim response As String
    response = HttpGet(API_BASE_URL & "pembelianbarang?nopembelian=" & URLEncode(nopembelian))
    GetPembelianbarangByNo = response
End Function

' Get Pembelianbarang by nopembelian and kodebarang
Public Function GetPembelianbarangByNoAndKode(nopembelian As String, kodebarang As String) As String
    Dim response As String
    response = HttpGet(API_BASE_URL & "pembelianbarang?nopembelian=" & URLEncode(nopembelian) & _
        "&kodebarang=" & URLEncode(kodebarang))
    GetPembelianbarangByNoAndKode = response
End Function

' Get All Pembelianbarang
Public Function GetAllPembelianbarang(Optional page As Long = 1, Optional perPage As Long = 100, _
    Optional search As String = "", Optional startDate As String = "", Optional endDate As String = "") As String
    
    Dim url As String
    url = API_BASE_URL & "pembelianbarang?page=" & page & "&per_page=" & perPage
    
    If search <> "" Then
        url = url & "&search=" & URLEncode(search)
    End If
    
    If startDate <> "" Then
        url = url & "&start_date=" & URLEncode(startDate)
    End If
    
    If endDate <> "" Then
        url = url & "&end_date=" & URLEncode(endDate)
    End If
    
    GetAllPembelianbarang = HttpGet(url)
End Function

' Update Pembelianbarang
Public Function UpdatePembelianbarang(id As Long, nopembelian As String, tanggalpembelian As String, _
    namasupplier As String, kodebarang As String, jumlah As Double, harga As Double, _
    discount As Double, totalharga As Double) As String
    
    Dim jsonData As String
    Dim response As String
    
    jsonData = "{"
    jsonData = jsonData & """id"": " & id & ","
    jsonData = jsonData & """nopembelian"": """ & EscapeJSON(nopembelian) & ""","
    jsonData = jsonData & """tanggalpembelian"": """ & tanggalpembelian & ""","
    jsonData = jsonData & """namasupplier"": """ & EscapeJSON(namasupplier) & ""","
    jsonData = jsonData & """kodebarang"": """ & EscapeJSON(kodebarang) & ""","
    jsonData = jsonData & """jumlah"": " & jumlah & ","
    jsonData = jsonData & """harga"": " & harga & ","
    jsonData = jsonData & """discount"": " & discount & ","
    jsonData = jsonData & """totalharga"": " & totalharga
    jsonData = jsonData & "}"
    
    response = HttpPut(API_BASE_URL & "pembelianbarang", jsonData)
    UpdatePembelianbarang = response
End Function

' Delete Pembelianbarang by ID
Public Function DeletePembelianbarangById(id As Long) As String
    Dim response As String
    response = HttpDelete(API_BASE_URL & "pembelianbarang?id=" & id)
    DeletePembelianbarangById = response
End Function

' Delete Pembelianbarang by nopembelian
Public Function DeletePembelianbarangByNo(nopembelian As String) As String
    Dim response As String
    response = HttpDelete(API_BASE_URL & "pembelianbarang?nopembelian=" & URLEncode(nopembelian))
    DeletePembelianbarangByNo = response
End Function

' ============================================
' PERUBAHANHARGA API FUNCTIONS
' ============================================

' Create Perubahanharga
Public Function CreatePerubahanharga(noperubahan As String, tanggalperubahan As String, _
    keterangan As String, kodebarang As String, hargalama As Double, discountlama As Double, _
    hargabaru As Double, discountbaru As Double) As String
    
    Dim jsonData As String
    Dim response As String
    
    jsonData = "{"
    jsonData = jsonData & """noperubahan"": """ & EscapeJSON(noperubahan) & ""","
    jsonData = jsonData & """tanggalperubahan"": """ & tanggalperubahan & ""","
    jsonData = jsonData & """keterangan"": """ & EscapeJSON(keterangan) & ""","
    jsonData = jsonData & """kodebarang"": """ & EscapeJSON(kodebarang) & ""","
    jsonData = jsonData & """hargalama"": " & hargalama & ","
    jsonData = jsonData & """discountlama"": " & discountlama & ","
    jsonData = jsonData & """hargabaru"": " & hargabaru & ","
    jsonData = jsonData & """discountbaru"": " & discountbaru
    jsonData = jsonData & "}"
    
    response = HttpPost(API_BASE_URL & "perubahanharga", jsonData)
    CreatePerubahanharga = response
End Function

' Get Perubahanharga by ID
Public Function GetPerubahanhargaById(id As Long) As String
    Dim response As String
    response = HttpGet(API_BASE_URL & "perubahanharga?id=" & id)
    GetPerubahanhargaById = response
End Function

' Get Perubahanharga by noperubahan
Public Function GetPerubahanhargaByNo(noperubahan As String) As String
    Dim response As String
    response = HttpGet(API_BASE_URL & "perubahanharga?noperubahan=" & URLEncode(noperubahan))
    GetPerubahanhargaByNo = response
End Function

' Get Perubahanharga by noperubahan and kodebarang
Public Function GetPerubahanhargaByNoAndKode(noperubahan As String, kodebarang As String) As String
    Dim response As String
    response = HttpGet(API_BASE_URL & "perubahanharga?noperubahan=" & URLEncode(noperubahan) & _
        "&kodebarang=" & URLEncode(kodebarang))
    GetPerubahanhargaByNoAndKode = response
End Function

' Get All Perubahanharga
Public Function GetAllPerubahanharga(Optional page As Long = 1, Optional perPage As Long = 100, _
    Optional search As String = "", Optional startDate As String = "", Optional endDate As String = "") As String
    
    Dim url As String
    url = API_BASE_URL & "perubahanharga?page=" & page & "&per_page=" & perPage
    
    If search <> "" Then
        url = url & "&search=" & URLEncode(search)
    End If
    
    If startDate <> "" Then
        url = url & "&start_date=" & URLEncode(startDate)
    End If
    
    If endDate <> "" Then
        url = url & "&end_date=" & URLEncode(endDate)
    End If
    
    GetAllPerubahanharga = HttpGet(url)
End Function

' Update Perubahanharga
Public Function UpdatePerubahanharga(id As Long, noperubahan As String, tanggalperubahan As String, _
    keterangan As String, kodebarang As String, hargalama As Double, discountlama As Double, _
    hargabaru As Double, discountbaru As Double) As String
    
    Dim jsonData As String
    Dim response As String
    
    jsonData = "{"
    jsonData = jsonData & """id"": " & id & ","
    jsonData = jsonData & """noperubahan"": """ & EscapeJSON(noperubahan) & ""","
    jsonData = jsonData & """tanggalperubahan"": """ & tanggalperubahan & ""","
    jsonData = jsonData & """keterangan"": """ & EscapeJSON(keterangan) & ""","
    jsonData = jsonData & """kodebarang"": """ & EscapeJSON(kodebarang) & ""","
    jsonData = jsonData & """hargalama"": " & hargalama & ","
    jsonData = jsonData & """discountlama"": " & discountlama & ","
    jsonData = jsonData & """hargabaru"": " & hargabaru & ","
    jsonData = jsonData & """discountbaru"": " & discountbaru
    jsonData = jsonData & "}"
    
    response = HttpPut(API_BASE_URL & "perubahanharga", jsonData)
    UpdatePerubahanharga = response
End Function

' Delete Perubahanharga by ID
Public Function DeletePerubahanhargaById(id As Long) As String
    Dim response As String
    response = HttpDelete(API_BASE_URL & "perubahanharga?id=" & id)
    DeletePerubahanhargaById = response
End Function

' Delete Perubahanharga by noperubahan
Public Function DeletePerubahanhargaByNo(noperubahan As String) As String
    Dim response As String
    response = HttpDelete(API_BASE_URL & "perubahanharga?noperubahan=" & URLEncode(noperubahan))
    DeletePerubahanhargaByNo = response
End Function

' ============================================
' HTTP HELPER FUNCTIONS
' ============================================

' HTTP GET Request
Private Function HttpGet(url As String) As String
    On Error GoTo ErrorHandler
    
    Dim xmlHttp As Object
    Set xmlHttp = CreateObject("MSXML2.XMLHTTP")
    
    xmlHttp.Open "GET", url, False
    xmlHttp.setRequestHeader "Content-Type", "application/json; charset=utf-8"
    xmlHttp.send
    
    HttpGet = xmlHttp.responseText
    Set xmlHttp = Nothing
    Exit Function
    
ErrorHandler:
    HttpGet = "{""success"": false, ""message"": """ & Err.Description & """}"
End Function

' HTTP POST Request
Private Function HttpPost(url As String, jsonData As String) As String
    On Error GoTo ErrorHandler
    
    Dim xmlHttp As Object
    Set xmlHttp = CreateObject("MSXML2.XMLHTTP")
    
    xmlHttp.Open "POST", url, False
    xmlHttp.setRequestHeader "Content-Type", "application/json; charset=utf-8"
    xmlHttp.send jsonData
    
    HttpPost = xmlHttp.responseText
    Set xmlHttp = Nothing
    Exit Function
    
ErrorHandler:
    HttpPost = "{""success"": false, ""message"": """ & Err.Description & """}"
End Function

' HTTP PUT Request
Private Function HttpPut(url As String, jsonData As String) As String
    On Error GoTo ErrorHandler
    
    Dim xmlHttp As Object
    Set xmlHttp = CreateObject("MSXML2.XMLHTTP")
    
    xmlHttp.Open "PUT", url, False
    xmlHttp.setRequestHeader "Content-Type", "application/json; charset=utf-8"
    xmlHttp.send jsonData
    
    HttpPut = xmlHttp.responseText
    Set xmlHttp = Nothing
    Exit Function
    
ErrorHandler:
    HttpPut = "{""success"": false, ""message"": """ & Err.Description & """}"
End Function

' HTTP DELETE Request
Private Function HttpDelete(url As String) As String
    On Error GoTo ErrorHandler
    
    Dim xmlHttp As Object
    Set xmlHttp = CreateObject("MSXML2.XMLHTTP")
    
    xmlHttp.Open "DELETE", url, False
    xmlHttp.setRequestHeader "Content-Type", "application/json; charset=utf-8"
    xmlHttp.send
    
    HttpDelete = xmlHttp.responseText
    Set xmlHttp = Nothing
    Exit Function
    
ErrorHandler:
    HttpDelete = "{""success"": false, ""message"": """ & Err.Description & """}"
End Function

' URL Encode
Private Function URLEncode(text As String) As String
    Dim i As Long
    Dim char As String
    Dim result As String
    
    result = ""
    For i = 1 To Len(text)
        char = Mid(text, i, 1)
        Select Case char
            Case " "
                result = result & "%20"
            Case "&"
                result = result & "%26"
            Case "+"
                result = result & "%2B"
            Case "="
                result = result & "%3D"
            Case "?"
                result = result & "%3F"
            Case "#"
                result = result & "%23"
            Case "/"
                result = result & "%2F"
            Case "\"
                result = result & "%5C"
            Case Else
                If Asc(char) > 127 Then
                    result = result & "%" & Right("0" & Hex(Asc(char)), 2)
                Else
                    result = result & char
                End If
        End Select
    Next i
    
    URLEncode = result
End Function

' JSON Escape
Private Function EscapeJSON(text As String) As String
    Dim result As String
    result = text
    result = Replace(result, "\", "\\")
    result = Replace(result, """", "\""")
    result = Replace(result, vbCrLf, "\n")
    result = Replace(result, vbCr, "\n")
    result = Replace(result, vbLf, "\n")
    result = Replace(result, vbTab, "\t")
    EscapeJSON = result
End Function

