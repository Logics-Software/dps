Attribute VB_Name = "modMasterSupplierAPI"
'---------------------------------------------------------------
' Module untuk API Mastersupplier
'---------------------------------------------------------------

Option Explicit

' Base URL API
Public Const API_BASE_URL_MASTERSUPPLIER As String = "http://localhost:8000/api/mastersupplier"

' ============================================
' MASTER SUPPLIER API WRAPPERS
' ============================================
Public Function GetAllMastersupplier(Optional page As Long = 1, Optional perPage As Long = 100, _
                                     Optional search As String = "", Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL_MASTERSUPPLIER & params
    GetAllMastersupplier = CallAPI("GET", url)
End Function

Public Function GetMastersupplierById(id As Long) As String
    Dim url As String
    url = API_BASE_URL_MASTERSUPPLIER & "?id=" & id
    GetMastersupplierById = CallAPI("GET", url)
End Function

Public Function GetMastersupplierByKode(kodesupplier As String) As String
    Dim url As String
    url = API_BASE_URL_MASTERSUPPLIER & "?kodesupplier=" & URLEncode(kodesupplier)
    GetMastersupplierByKode = CallAPI("GET", url)
End Function

Public Function CreateMastersupplier(kodesupplier As String, namasupplier As String, _
                                     Optional alamatsupplier As String = "", _
                                     Optional notelepon As String = "", _
                                     Optional kontakperson As String = "", _
                                     Optional status As String = "aktif") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_MASTERSUPPLIER
    postData = "kodesupplier=" & URLEncode(kodesupplier) & _
               "&namasupplier=" & URLEncode(namasupplier)
    
    If alamatsupplier <> "" Then postData = postData & "&alamatsupplier=" & URLEncode(alamatsupplier)
    If notelepon <> "" Then postData = postData & "&notelepon=" & URLEncode(notelepon)
    If kontakperson <> "" Then postData = postData & "&kontakperson=" & URLEncode(kontakperson)
    postData = postData & "&status=" & URLEncode(status)
    
    CreateMastersupplier = CallAPI("POST", url, postData)
End Function

Public Function UpdateMastersupplier(id As Long, _
                                     Optional kodesupplier As String = "", _
                                     Optional namasupplier As String = "", _
                                     Optional alamatsupplier As String = "", _
                                     Optional notelepon As String = "", _
                                     Optional kontakperson As String = "", _
                                     Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_MASTERSUPPLIER
    postData = "_method=PUT&id=" & id
    hasData = False
    
    If kodesupplier <> "" Then postData = postData & "&kodesupplier=" & URLEncode(kodesupplier): hasData = True
    If namasupplier <> "" Then postData = postData & "&namasupplier=" & URLEncode(namasupplier): hasData = True
    If alamatsupplier <> "" Then postData = postData & "&alamatsupplier=" & URLEncode(alamatsupplier): hasData = True
    If notelepon <> "" Then postData = postData & "&notelepon=" & URLEncode(notelepon): hasData = True
    If kontakperson <> "" Then postData = postData & "&kontakperson=" & URLEncode(kontakperson): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        UpdateMastersupplier = CallAPI("POST", url, postData)
    Else
        UpdateMastersupplier = "ERROR: No data to update"
    End If
End Function

Public Function PatchMastersupplier(id As Long, _
                                    Optional kodesupplier As String = "", _
                                    Optional namasupplier As String = "", _
                                    Optional alamatsupplier As String = "", _
                                    Optional notelepon As String = "", _
                                    Optional kontakperson As String = "", _
                                    Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_MASTERSUPPLIER
    postData = "_method=PATCH&id=" & id
    hasData = False
    
    If kodesupplier <> "" Then postData = postData & "&kodesupplier=" & URLEncode(kodesupplier): hasData = True
    If namasupplier <> "" Then postData = postData & "&namasupplier=" & URLEncode(namasupplier): hasData = True
    If alamatsupplier <> "" Then postData = postData & "&alamatsupplier=" & URLEncode(alamatsupplier): hasData = True
    If notelepon <> "" Then postData = postData & "&notelepon=" & URLEncode(notelepon): hasData = True
    If kontakperson <> "" Then postData = postData & "&kontakperson=" & URLEncode(kontakperson): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        PatchMastersupplier = CallAPI("POST", url, postData)
    Else
        PatchMastersupplier = "ERROR: No data to update"
    End If
End Function

Public Function DeleteMastersupplierById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_MASTERSUPPLIER
    postData = "_method=DELETE&id=" & id
    
    DeleteMastersupplierById = CallAPI("POST", url, postData)
End Function

Public Function DeleteMastersupplierByKode(kodesupplier As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_MASTERSUPPLIER
    postData = "_method=DELETE&kodesupplier=" & URLEncode(kodesupplier)
    
    DeleteMastersupplierByKode = CallAPI("POST", url, postData)
End Function


