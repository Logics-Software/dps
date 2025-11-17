Attribute VB_Name = "modTabelGolonganAPI"
'---------------------------------------------------------------
' Module untuk API Tabelgolongan
'---------------------------------------------------------------

Option Explicit

' Base URL API
Public Const API_BASE_URL_TABELGOLONGAN As String = "http://localhost:8000/api/tabelgolongan"

' ============================================
' TABEL GOLONGAN API WRAPPERS
' ============================================
Public Function GetAllTabelgolongan(Optional page As Long = 1, Optional perPage As Long = 100, _
                                    Optional search As String = "", Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL_TABELGOLONGAN & params
    GetAllTabelgolongan = CallAPI("GET", url)
End Function

Public Function GetTabelgolonganById(id As Long) As String
    Dim url As String
    url = API_BASE_URL_TABELGOLONGAN & "?id=" & id
    GetTabelgolonganById = CallAPI("GET", url)
End Function

Public Function GetTabelgolonganByKode(kodegolongan As String) As String
    Dim url As String
    url = API_BASE_URL_TABELGOLONGAN & "?kodegolongan=" & URLEncode(kodegolongan)
    GetTabelgolonganByKode = CallAPI("GET", url)
End Function

Public Function CreateTabelgolongan(kodegolongan As String, namagolongan As String, _
                                    Optional status As String = "aktif") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_TABELGOLONGAN
    postData = "kodegolongan=" & URLEncode(kodegolongan) & _
               "&namagolongan=" & URLEncode(namagolongan) & _
               "&status=" & URLEncode(status)
    
    CreateTabelgolongan = CallAPI("POST", url, postData)
End Function

Public Function UpdateTabelgolongan(id As Long, _
                                    Optional kodegolongan As String = "", _
                                    Optional namagolongan As String = "", _
                                    Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_TABELGOLONGAN
    postData = "_method=PUT&id=" & id
    hasData = False
    
    If kodegolongan <> "" Then postData = postData & "&kodegolongan=" & URLEncode(kodegolongan): hasData = True
    If namagolongan <> "" Then postData = postData & "&namagolongan=" & URLEncode(namagolongan): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        UpdateTabelgolongan = CallAPI("POST", url, postData)
    Else
        UpdateTabelgolongan = "ERROR: No data to update"
    End If
End Function

Public Function PatchTabelgolongan(id As Long, _
                                   Optional kodegolongan As String = "", _
                                   Optional namagolongan As String = "", _
                                   Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_TABELGOLONGAN
    postData = "_method=PATCH&id=" & id
    hasData = False
    
    If kodegolongan <> "" Then postData = postData & "&kodegolongan=" & URLEncode(kodegolongan): hasData = True
    If namagolongan <> "" Then postData = postData & "&namagolongan=" & URLEncode(namagolongan): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        PatchTabelgolongan = CallAPI("POST", url, postData)
    Else
        PatchTabelgolongan = "ERROR: No data to update"
    End If
End Function

Public Function DeleteTabelgolonganById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_TABELGOLONGAN
    postData = "_method=DELETE&id=" & id
    
    DeleteTabelgolonganById = CallAPI("POST", url, postData)
End Function

Public Function DeleteTabelgolonganByKode(kodegolongan As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_TABELGOLONGAN
    postData = "_method=DELETE&kodegolongan=" & URLEncode(kodegolongan)
    
    DeleteTabelgolonganByKode = CallAPI("POST", url, postData)
End Function


