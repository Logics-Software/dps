Attribute VB_Name = "modSalesAPI"
' Module untuk API Mastersales & Mastercustomer
' Copy kode ini ke dalam Module di VB6

Option Explicit

' Base URL API - Sesuaikan dengan server Anda
Public Const API_BASE_URL As String = "http://localhost:8000/api/mastersales"

' ============================================
' GET - Ambil Semua Data
' ============================================
Public Function GetAllMastersales(Optional page As Long = 1, Optional perPage As Long = 100, _
                                   Optional search As String = "", Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL & params
    GetAllMastersales = CallAPI("GET", url)
End Function

' ============================================
' GET - Ambil Data by ID
' ============================================
Public Function GetMastersalesById(id As Long) As String
    Dim url As String
    url = API_BASE_URL & "?id=" & id
    GetMastersalesById = CallAPI("GET", url)
End Function

' ============================================
' GET - Ambil Data by Kode Sales
' ============================================
Public Function GetMastersalesByKode(kodesales As String) As String
    Dim url As String
    url = API_BASE_URL & "?kodesales=" & URLEncode(kodesales)
    GetMastersalesByKode = CallAPI("GET", url)
End Function

' ============================================
' POST - Create Data Baru (Form URL Encoded)
' ============================================
Public Function CreateMastersales(kodesales As String, namasales As String, _
                                   Optional alamatsales As String = "", _
                                   Optional notelepon As String = "", _
                                   Optional status As String = "aktif") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    
    postData = "kodesales=" & URLEncode(kodesales) & _
               "&namasales=" & URLEncode(namasales) & _
               "&alamatsales=" & URLEncode(alamatsales) & _
               "&notelepon=" & URLEncode(notelepon) & _
               "&status=" & URLEncode(status)
    
    CreateMastersales = CallAPI("POST", url, postData)
End Function

' ============================================
' PUT - Update Data (Form URL Encoded)
' ============================================
Public Function UpdateMastersales(id As Long, Optional kodesales As String = "", _
                                    Optional namasales As String = "", _
                                    Optional alamatsales As String = "", _
                                    Optional notelepon As String = "", _
                                    Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL
    postData = "_method=PUT&id=" & id
    hasData = False
    
    If kodesales <> "" Then
        postData = postData & "&kodesales=" & URLEncode(kodesales)
        hasData = True
    End If
    If namasales <> "" Then
        postData = postData & "&namasales=" & URLEncode(namasales)
        hasData = True
    End If
    If alamatsales <> "" Then
        postData = postData & "&alamatsales=" & URLEncode(alamatsales)
        hasData = True
    End If
    If notelepon <> "" Then
        postData = postData & "&notelepon=" & URLEncode(notelepon)
        hasData = True
    End If
    If status <> "" Then
        postData = postData & "&status=" & URLEncode(status)
        hasData = True
    End If
    
    If hasData Then
        UpdateMastersales = CallAPI("POST", url, postData)
    Else
        UpdateMastersales = "ERROR: No data to update"
    End If
End Function

' ============================================
' PATCH - Partial Update (Form URL Encoded)
' ============================================
Public Function PatchMastersales(id As Long, Optional kodesales As String = "", _
                                   Optional namasales As String = "", _
                                   Optional alamatsales As String = "", _
                                   Optional notelepon As String = "", _
                                   Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL
    postData = "_method=PATCH&id=" & id
    hasData = False
    
    If kodesales <> "" Then
        postData = postData & "&kodesales=" & URLEncode(kodesales)
        hasData = True
    End If
    If namasales <> "" Then
        postData = postData & "&namasales=" & URLEncode(namasales)
        hasData = True
    End If
    If alamatsales <> "" Then
        postData = postData & "&alamatsales=" & URLEncode(alamatsales)
        hasData = True
    End If
    If notelepon <> "" Then
        postData = postData & "&notelepon=" & URLEncode(notelepon)
        hasData = True
    End If
    If status <> "" Then
        postData = postData & "&status=" & URLEncode(status)
        hasData = True
    End If
    
    If hasData Then
        PatchMastersales = CallAPI("POST", url, postData)
    Else
        PatchMastersales = "ERROR: No data to update"
    End If
End Function

' ============================================
' DELETE - Hapus Data by ID
' ============================================
Public Function DeleteMastersalesById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=DELETE&id=" & id
    
    DeleteMastersalesById = CallAPI("POST", url, postData)
End Function

' ============================================
' DELETE - Hapus Data by Kode Sales
' ============================================
Public Function DeleteMastersalesByKode(kodesales As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=DELETE&kodesales=" & URLEncode(kodesales)
    
    DeleteMastersalesByKode = CallAPI("POST", url, postData)
End Function
