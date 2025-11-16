Attribute VB_Name = "modMasterBarangAPI"
'---------------------------------------------------------------
' Module untuk API Masterbarang
'---------------------------------------------------------------

Option Explicit

' Base URL API
Public Const API_BASE_URL_MASTERBARANG As String = "http://localhost:8000/api/masterbarang"

' ============================================
' MASTER BARANG API WRAPPERS
' ============================================
Public Function GetAllMasterbarang(Optional page As Long = 1, Optional perPage As Long = 100, _
                                   Optional search As String = "", _
                                   Optional kodepabrik As String = "", _
                                   Optional kodegolongan As String = "", _
                                   Optional kodesupplier As String = "", _
                                   Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If kodepabrik <> "" Then params = params & "&kodepabrik=" & URLEncode(kodepabrik)
    If kodegolongan <> "" Then params = params & "&kodegolongan=" & URLEncode(kodegolongan)
    If kodesupplier <> "" Then params = params & "&kodesupplier=" & URLEncode(kodesupplier)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL_MASTERBARANG & params
    GetAllMasterbarang = CallAPI("GET", url)
End Function

Public Function GetMasterbarangById(id As Long) As String
    Dim url As String
    url = API_BASE_URL_MASTERBARANG & "?id=" & id
    GetMasterbarangById = CallAPI("GET", url)
End Function

Public Function GetMasterbarangByKode(kodebarang As String) As String
    Dim url As String
    url = API_BASE_URL_MASTERBARANG & "?kodebarang=" & URLEncode(kodebarang)
    GetMasterbarangByKode = CallAPI("GET", url)
End Function

Public Function CreateMasterbarang(kodebarang As String, namabarang As String, _
                                   Optional satuan As String = "", _
                                   Optional kodepabrik As String = "", _
                                   Optional kodegolongan As String = "", _
                                   Optional kodesupplier As String = "", _
                                   Optional kandungan As String = "", _
                                   Optional oot As String = "tidak", _
                                   Optional prekursor As String = "tidak", _
                                   Optional nie As String = "", _
                                   Optional hpp As String = "", _
                                   Optional hargabeli As String = "", _
                                   Optional discountbeli As String = "", _
                                   Optional hargajual As String = "", _
                                   Optional discountjual As String = "", _
                                   Optional stokakhir As String = "", _
                                   Optional status As String = "aktif") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_MASTERBARANG
    postData = "kodebarang=" & URLEncode(kodebarang) & _
               "&namabarang=" & URLEncode(namabarang)
    
    If satuan <> "" Then postData = postData & "&satuan=" & URLEncode(satuan)
    If kodepabrik <> "" Then postData = postData & "&kodepabrik=" & URLEncode(kodepabrik)
    If kodegolongan <> "" Then postData = postData & "&kodegolongan=" & URLEncode(kodegolongan)
    If kodesupplier <> "" Then postData = postData & "&kodesupplier=" & URLEncode(kodesupplier)
    If kandungan <> "" Then postData = postData & "&kandungan=" & URLEncode(kandungan)
    postData = postData & "&oot=" & URLEncode(oot)
    postData = postData & "&prekursor=" & URLEncode(prekursor)
    If nie <> "" Then postData = postData & "&nie=" & URLEncode(nie)
    If hpp <> "" Then postData = postData & "&hpp=" & URLEncode(hpp)
    If hargabeli <> "" Then postData = postData & "&hargabeli=" & URLEncode(hargabeli)
    If discountbeli <> "" Then postData = postData & "&discountbeli=" & URLEncode(discountbeli)
    If hargajual <> "" Then postData = postData & "&hargajual=" & URLEncode(hargajual)
    If discountjual <> "" Then postData = postData & "&discountjual=" & URLEncode(discountjual)
    If stokakhir <> "" Then postData = postData & "&stokakhir=" & URLEncode(stokakhir)
    If status <> "" Then postData = postData & "&status=" & URLEncode(status)
    
    CreateMasterbarang = CallAPI("POST", url, postData)
End Function

Public Function UpdateMasterbarang(id As Long, _
                                   Optional kodebarang As String = "", _
                                   Optional namabarang As String = "", _
                                   Optional satuan As String = "", _
                                   Optional kodepabrik As String = "", _
                                   Optional kodegolongan As String = "", _
                                   Optional kodesupplier As String = "", _
                                   Optional kandungan As String = "", _
                                   Optional oot As String = "", _
                                   Optional prekursor As String = "", _
                                   Optional nie As String = "", _
                                   Optional hpp As String = "", _
                                   Optional hargabeli As String = "", _
                                   Optional discountbeli As String = "", _
                                   Optional hargajual As String = "", _
                                   Optional discountjual As String = "", _
                                   Optional stokakhir As String = "", _
                                   Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_MASTERBARANG
    postData = "_method=PUT&id=" & id
    hasData = False
    
    If kodebarang <> "" Then postData = postData & "&kodebarang=" & URLEncode(kodebarang): hasData = True
    If namabarang <> "" Then postData = postData & "&namabarang=" & URLEncode(namabarang): hasData = True
    If satuan <> "" Then postData = postData & "&satuan=" & URLEncode(satuan): hasData = True
    If kodepabrik <> "" Then postData = postData & "&kodepabrik=" & URLEncode(kodepabrik): hasData = True
    If kodegolongan <> "" Then postData = postData & "&kodegolongan=" & URLEncode(kodegolongan): hasData = True
    If kodesupplier <> "" Then postData = postData & "&kodesupplier=" & URLEncode(kodesupplier): hasData = True
    If kandungan <> "" Then postData = postData & "&kandungan=" & URLEncode(kandungan): hasData = True
    If oot <> "" Then postData = postData & "&oot=" & URLEncode(oot): hasData = True
    If prekursor <> "" Then postData = postData & "&prekursor=" & URLEncode(prekursor): hasData = True
    If nie <> "" Then postData = postData & "&nie=" & URLEncode(nie): hasData = True
    If hpp <> "" Then postData = postData & "&hpp=" & URLEncode(hpp): hasData = True
    If hargabeli <> "" Then postData = postData & "&hargabeli=" & URLEncode(hargabeli): hasData = True
    If discountbeli <> "" Then postData = postData & "&discountbeli=" & URLEncode(discountbeli): hasData = True
    If hargajual <> "" Then postData = postData & "&hargajual=" & URLEncode(hargajual): hasData = True
    If discountjual <> "" Then postData = postData & "&discountjual=" & URLEncode(discountjual): hasData = True
    If stokakhir <> "" Then postData = postData & "&stokakhir=" & URLEncode(stokakhir): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        UpdateMasterbarang = CallAPI("POST", url, postData)
    Else
        UpdateMasterbarang = "ERROR: No data to update"
    End If
End Function

Public Function PatchMasterbarang(id As Long, _
                                  Optional kodebarang As String = "", _
                                  Optional namabarang As String = "", _
                                  Optional satuan As String = "", _
                                  Optional kodepabrik As String = "", _
                                  Optional kodegolongan As String = "", _
                                  Optional kodesupplier As String = "", _
                                  Optional kandungan As String = "", _
                                  Optional oot As String = "", _
                                  Optional prekursor As String = "", _
                                  Optional nie As String = "", _
                                  Optional hpp As String = "", _
                                  Optional hargabeli As String = "", _
                                  Optional discountbeli As String = "", _
                                  Optional hargajual As String = "", _
                                  Optional discountjual As String = "", _
                                  Optional stokakhir As String = "", _
                                  Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL_MASTERBARANG
    postData = "_method=PATCH&id=" & id
    hasData = False
    
    If kodebarang <> "" Then postData = postData & "&kodebarang=" & URLEncode(kodebarang): hasData = True
    If namabarang <> "" Then postData = postData & "&namabarang=" & URLEncode(namabarang): hasData = True
    If satuan <> "" Then postData = postData & "&satuan=" & URLEncode(satuan): hasData = True
    If kodepabrik <> "" Then postData = postData & "&kodepabrik=" & URLEncode(kodepabrik): hasData = True
    If kodegolongan <> "" Then postData = postData & "&kodegolongan=" & URLEncode(kodegolongan): hasData = True
    If kodesupplier <> "" Then postData = postData & "&kodesupplier=" & URLEncode(kodesupplier): hasData = True
    If kandungan <> "" Then postData = postData & "&kandungan=" & URLEncode(kandungan): hasData = True
    If oot <> "" Then postData = postData & "&oot=" & URLEncode(oot): hasData = True
    If prekursor <> "" Then postData = postData & "&prekursor=" & URLEncode(prekursor): hasData = True
    If nie <> "" Then postData = postData & "&nie=" & URLEncode(nie): hasData = True
    If hpp <> "" Then postData = postData & "&hpp=" & URLEncode(hpp): hasData = True
    If hargabeli <> "" Then postData = postData & "&hargabeli=" & URLEncode(hargabeli): hasData = True
    If discountbeli <> "" Then postData = postData & "&discountbeli=" & URLEncode(discountbeli): hasData = True
    If hargajual <> "" Then postData = postData & "&hargajual=" & URLEncode(hargajual): hasData = True
    If discountjual <> "" Then postData = postData & "&discountjual=" & URLEncode(discountjual): hasData = True
    If stokakhir <> "" Then postData = postData & "&stokakhir=" & URLEncode(stokakhir): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        PatchMasterbarang = CallAPI("POST", url, postData)
    Else
        PatchMasterbarang = "ERROR: No data to update"
    End If
End Function

Public Function DeleteMasterbarangById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_MASTERBARANG
    postData = "_method=DELETE&id=" & id
    
    DeleteMasterbarangById = CallAPI("POST", url, postData)
End Function

Public Function DeleteMasterbarangByKode(kodebarang As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL_MASTERBARANG
    postData = "_method=DELETE&kodebarang=" & URLEncode(kodebarang)
    
    DeleteMasterbarangByKode = CallAPI("POST", url, postData)
End Function


