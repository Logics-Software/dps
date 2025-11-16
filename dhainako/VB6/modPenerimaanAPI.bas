Attribute VB_Name = "modPenerimaanAPI"
'---------------------------------------------------------------
' Module untuk API Penerimaan Piutang (Header & Detail)
'---------------------------------------------------------------

Option Explicit

' Base URL API Penerimaan
Public Const API_BASE_URL_PENERIMAAN As String = "http://localhost:8000/api/penerimaan"

' ============================================================
' PENERIMAAN API WRAPPERS
' ============================================================

Public Function GetPenerimaan(Optional page As Long = 1, Optional perPage As Long = 20, _
                              Optional search As String = "", _
                              Optional status As String = "", _
                              Optional kodecustomer As String = "", _
                              Optional kodesales As String = "", _
                              Optional startDate As String = "", _
                              Optional endDate As String = "") As String
    Dim url As String
    Dim params As String

    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    If kodecustomer <> "" Then params = params & "&kodecustomer=" & URLEncode(kodecustomer)
    If kodesales <> "" Then params = params & "&kodesales=" & URLEncode(kodesales)
    If startDate <> "" Then params = params & "&start_date=" & URLEncode(startDate)
    If endDate <> "" Then params = params & "&end_date=" & URLEncode(endDate)

    url = API_BASE_URL_PENERIMAAN & params
    GetPenerimaan = CallAPI("GET", url)
End Function

Public Function GetPenerimaanByNo(nopenerimaan As String) As String
    Dim url As String
    url = API_BASE_URL_PENERIMAAN & "?nopenerimaan=" & URLEncode(nopenerimaan)
    GetPenerimaanByNo = CallAPI("GET", url)
End Function

Public Function CreatePenerimaan(jsonPayload As String) As String
    CreatePenerimaan = CallAPI("POST", API_BASE_URL_PENERIMAAN, jsonPayload)
End Function

Public Function UpdatePenerimaan(jsonPayload As String) As String
    UpdatePenerimaan = CallAPI("PUT", API_BASE_URL_PENERIMAAN, jsonPayload)
End Function

Public Function PatchPenerimaan(jsonPayload As String) As String
    PatchPenerimaan = CallAPI("PATCH", API_BASE_URL_PENERIMAAN, jsonPayload)
End Function

Public Function DeletePenerimaan(nopenerimaan As String) As String
    Dim postData As String
    postData = "_method=DELETE&nopenerimaan=" & URLEncode(nopenerimaan)
    DeletePenerimaan = CallAPI("POST", API_BASE_URL_PENERIMAAN, postData)
End Function

' ============================================================
' UPDATE STATUS DAN NOINKASO (untuk bridging VB6)
' ============================================================

Public Function UpdatePenerimaanStatus(nopenerimaan As String, status As String, Optional noinkaso As String = "") As String
    Dim payload As String
    payload = "{""action"":""update_status"",""nopenerimaan"":""" & Replace(nopenerimaan, """", "\""") & """,""status"":""" & Replace(status, """", "\""") & """"
    If noinkaso <> "" Then
        payload = payload & ",""noinkaso"":""" & Replace(noinkaso, """", "\""") & """"
    End If
    payload = payload & "}"
    UpdatePenerimaanStatus = CallAPI("POST", API_BASE_URL_PENERIMAAN, payload)
End Function

' ============================================================
' HELPER FUNCTIONS
' ============================================================

' Fungsi untuk membangun JSON payload untuk Create/Update Penerimaan
Public Function BuildPenerimaanJSON(nopenerimaan As String, _
                                    tanggalpenerimaan As String, _
                                    Optional statuspkp As String = "", _
                                    Optional jenispenerimaan As String = "", _
                                    Optional kodesales As String = "", _
                                    Optional kodecustomer As String = "", _
                                    Optional totalpiutang As String = "0", _
                                    Optional totalpotongan As String = "0", _
                                    Optional totallainlain As String = "0", _
                                    Optional totalnetto As String = "0", _
                                    Optional status As String = "belumproses", _
                                    Optional noinkaso As String = "", _
                                    Optional detailsJSON As String = "[]") As String
    Dim json As String
    json = "{"
    json = json & """nopenerimaan"":""" & Replace(nopenerimaan, """", "\""") & """"
    json = json & ",""tanggalpenerimaan"":""" & Replace(tanggalpenerimaan, """", "\""") & """"
    If statuspkp <> "" Then json = json & ",""statuspkp"":""" & Replace(statuspkp, """", "\""") & """"
    If jenispenerimaan <> "" Then json = json & ",""jenispenerimaan"":""" & Replace(jenispenerimaan, """", "\""") & """"
    If kodesales <> "" Then json = json & ",""kodesales"":""" & Replace(kodesales, """", "\""") & """"
    If kodecustomer <> "" Then json = json & ",""kodecustomer"":""" & Replace(kodecustomer, """", "\""") & """"
    json = json & ",""totalpiutang"":""" & Replace(totalpiutang, """", "\""") & """"
    json = json & ",""totalpotongan"":""" & Replace(totalpotongan, """", "\""") & """"
    json = json & ",""totallainlain"":""" & Replace(totallainlain, """", "\""") & """"
    json = json & ",""totalnetto"":""" & Replace(totalnetto, """", "\""") & """"
    json = json & ",""status"":""" & Replace(status, """", "\""") & """"
    If noinkaso <> "" Then json = json & ",""noinkaso"":""" & Replace(noinkaso, """", "\""") & """"
    json = json & ",""details"":" & detailsJSON
    json = json & "}"
    BuildPenerimaanJSON = json
End Function

' Fungsi untuk membangun JSON array untuk detail penerimaan
Public Function BuildDetailPenerimaanJSON(nopenjualan As String, _
                                          Optional nogiro As String = "", _
                                          Optional tanggalcair As String = "", _
                                          Optional piutang As String = "0", _
                                          Optional potongan As String = "0", _
                                          Optional lainlain As String = "0", _
                                          Optional netto As String = "0", _
                                          Optional nourut As Long = 0) As String
    Dim json As String
    json = "{"
    json = json & """nopenjualan"":""" & Replace(nopenjualan, """", "\""") & """"
    If nogiro <> "" Then json = json & ",""nogiro"":""" & Replace(nogiro, """", "\""") & """"
    If tanggalcair <> "" Then json = json & ",""tanggalcair"":""" & Replace(tanggalcair, """", "\""") & """"
    json = json & ",""piutang"":""" & Replace(piutang, """", "\""") & """"
    json = json & ",""potongan"":""" & Replace(potongan, """", "\""") & """"
    json = json & ",""lainlain"":""" & Replace(lainlain, """", "\""") & """"
    json = json & ",""netto"":""" & Replace(netto, """", "\""") & """"
    If nourut > 0 Then json = json & ",""nourut"":" & nourut
    json = json & "}"
    BuildDetailPenerimaanJSON = json
End Function

' Fungsi untuk membangun array JSON dari multiple details
Public Function BuildDetailsArrayJSON(details() As String) As String
    Dim json As String
    Dim i As Long
    json = "["
    For i = LBound(details) To UBound(details)
        If i > LBound(details) Then json = json & ","
        json = json & details(i)
    Next i
    json = json & "]"
    BuildDetailsArrayJSON = json
End Function

