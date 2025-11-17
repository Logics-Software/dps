Attribute VB_Name = "modPenjualanAPI"
'---------------------------------------------------------------
' Module untuk API Penjualan (Header & Detail)
'---------------------------------------------------------------

Option Explicit

' Base URL API Penjualan
Public Const API_BASE_URL_PENJUALAN As String = "http://localhost:8000/api/penjualan"

' ============================================================
' PENJUALAN API WRAPPERS
' ============================================================

Public Function GetPenjualan(Optional page As Long = 1, Optional perPage As Long = 20, _
                              Optional search As String = "", _
                              Optional periode As String = "today", _
                              Optional startDate As String = "", _
                              Optional endDate As String = "", _
                              Optional statuspkp As String = "") As String
    Dim url As String
    Dim params As String

    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If periode <> "" Then params = params & "&periode=" & URLEncode(periode)
    If startDate <> "" Then params = params & "&start_date=" & URLEncode(startDate)
    If endDate <> "" Then params = params & "&end_date=" & URLEncode(endDate)
    If statuspkp <> "" Then params = params & "&statuspkp=" & URLEncode(statuspkp)

    url = API_BASE_URL_PENJUALAN & params
    GetPenjualan = CallAPI("GET", url)
End Function

Public Function GetPenjualanByNo(nopenjualan As String) As String
    Dim url As String
    url = API_BASE_URL_PENJUALAN & "?nopenjualan=" & URLEncode(nopenjualan)
    GetPenjualanByNo = CallAPI("GET", url)
End Function

Public Function CreatePenjualan(jsonPayload As String) As String
    CreatePenjualan = CallAPI("POST", API_BASE_URL_PENJUALAN, jsonPayload)
End Function

Public Function UpdatePenjualan(jsonPayload As String) As String
    UpdatePenjualan = CallAPI("PUT", API_BASE_URL_PENJUALAN, jsonPayload)
End Function

Public Function PatchPenjualan(jsonPayload As String) As String
    PatchPenjualan = CallAPI("PATCH", API_BASE_URL_PENJUALAN, jsonPayload)
End Function

Public Function DeletePenjualan(nopenjualan As String) As String
    Dim postData As String
    postData = "_method=DELETE&nopenjualan=" & URLEncode(nopenjualan)
    DeletePenjualan = CallAPI("POST", API_BASE_URL_PENJUALAN, postData)
End Function

Public Function UpdatePenjualanSaldo(nopenjualan As String, saldopenjualan As String) As String
    Dim payload As String
    payload = "{""action"":""update_saldo"",""nopenjualan"":""" & Replace(nopenjualan, """", "\""") & """,""saldopenjualan"":""" & Replace(saldopenjualan, """", "\""") & """}"
    UpdatePenjualanSaldo = CallAPI("POST", API_BASE_URL_PENJUALAN, payload)
End Function

Public Function UpdatePenjualanOrderInfo(nopenjualan As String, noorder As String, tanggalorder As String) As String
    Dim payload As String
    payload = "{""action"":""update_order_info"",""nopenjualan"":""" & Replace(nopenjualan, """", "\""") & """,""noorder"":""" & Replace(noorder, """", "\""") & """,""tanggalorder"":""" & Replace(tanggalorder, """", "\""") & """}"
    UpdatePenjualanOrderInfo = CallAPI("POST", API_BASE_URL_PENJUALAN, payload)
End Function


