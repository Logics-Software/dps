Attribute VB_Name = "modTabelPabrikAPI"
'---------------------------------------------------------------
' Module untuk API Tabelpabrik
'---------------------------------------------------------------

Option Explicit

' Base URL API
Public Const API_BASE_URL_TABELPABRIK As String = "http://localhost:8000/api/tabelpabrik"

' ============================================
' TABEL PABRIK API WRAPPERS
' ============================================
Public Function GetAllTabelpabrik(Optional page As Long = 1, Optional perPage As Long = 100, _
                                  Optional search As String = "", Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL_TABELPABRIK & params
    GetAllTabelpabrik = CallAPI("GET", url)
End Function

Public Function GetTabelpabrikById(id As Long) As String
    Dim url As String
    url = API_BASE_URL_TABELPABRIK & "?id=" & id
    GetTabelpabrikById = CallAPI("GET", url)
End Function

Public Function GetTabelpabrikByKode(kodepabrik As String) As String
    Dim url As String
    url = API_BASE_URL_TABELPABRIK & "?kodepabrik=" & URLEncode(kodepabrik)
    GetTabelpabrikByKode = CallAPI("GET", url)
End Function

Public Function CreateTabelpabrik(kodepabrik As String, namapabrik As String, _
                                  Optional status As String = "aktif") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_TABELPABRIK
    postData = "kodepabrik=" & URLEncode(kodepabrik) & _
               "&namapabrik=" & URLEncode(namapabrik) & _
               "&status=" & URLEncode(status)
    
    CreateTabelpabrik = CallAPI("POST", url, postData)
End Function

Public Function UpdateTabelpabrik(id As Long, _
                                  Optional kodepabrik As String = "", _
                                  Optional namapabrik As String = "", _
                                  Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_TABELPABRIK
    postData = "_method=PUT&id=" & id
    hasData = False
    
    If kodepabrik <> "" Then postData = postData & "&kodepabrik=" & URLEncode(kodepabrik): hasData = True
    If namapabrik <> "" Then postData = postData & "&namapabrik=" & URLEncode(namapabrik): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        UpdateTabelpabrik = CallAPI("POST", url, postData)
    Else
        UpdateTabelpabrik = "ERROR: No data to update"
    End If
End Function

Public Function PatchTabelpabrik(id As Long, _
                                 Optional kodepabrik As String = "", _
                                 Optional namapabrik As String = "", _
                                 Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_TABELPABRIK
    postData = "_method=PATCH&id=" & id
    hasData = False
    
    If kodepabrik <> "" Then postData = postData & "&kodepabrik=" & URLEncode(kodepabrik): hasData = True
    If namapabrik <> "" Then postData = postData & "&namapabrik=" & URLEncode(namapabrik): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        PatchTabelpabrik = CallAPI("POST", url, postData)
    Else
        PatchTabelpabrik = "ERROR: No data to update"
    End If
End Function

Public Function DeleteTabelpabrikById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_TABELPABRIK
    postData = "_method=DELETE&id=" & id
    
    DeleteTabelpabrikById = CallAPI("POST", url, postData)
End Function

Public Function DeleteTabelpabrikByKode(kodepabrik As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_TABELPABRIK
    postData = "_method=DELETE&kodepabrik=" & URLEncode(kodepabrik)
    
    DeleteTabelpabrikByKode = CallAPI("POST", url, postData)
End Function


