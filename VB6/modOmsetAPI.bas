Attribute VB_Name = "modOmsetAPI"
'---------------------------------------------------------------
' Module untuk API Omset
'---------------------------------------------------------------

Option Explicit

' Base URL API Omset
Public Const API_BASE_URL_OMSET As String = "http://localhost:8000/api/omset"

' ============================================================
' OMSET API WRAPPERS
' ============================================================

' Find Omset berdasarkan tahun, bulan, kodesales
Public Function FindOmset(tahun As String, bulan As String, kodesales As String) As String
    Dim url As String
    Dim params As String
    
    params = "?tahun=" & URLEncode(tahun) & "&bulan=" & URLEncode(bulan) & "&kodesales=" & URLEncode(kodesales)
    url = API_BASE_URL_OMSET & params
    FindOmset = CallAPI("GET", url)
End Function

' Add Omset (POST)
Public Function AddOmset(jsonPayload As String) As String
    AddOmset = CallAPI("POST", API_BASE_URL_OMSET, jsonPayload)
End Function

' Delete Omset berdasarkan tahun dan bulan
Public Function DeleteOmset(tahun As String, bulan As String) As String
    Dim url As String
    Dim params As String
    
    params = "?tahun=" & URLEncode(tahun) & "&bulan=" & URLEncode(bulan)
    url = API_BASE_URL_OMSET & params
    DeleteOmset = CallAPI("DELETE", url)
End Function

' Helper function untuk membuat JSON payload dari data omset
Public Function BuildOmsetJSON(tahun As String, bulan As String, kodesales As String, _
                                Optional namasales As String = "", _
                                Optional jumlahfaktur As Double = 0, _
                                Optional penjualan As Double = 0, _
                                Optional returpenjualan As Double = 0, _
                                Optional penjualanbersih As Double = 0, _
                                Optional targetpenjualan As Double = 0, _
                                Optional prosenpenjualan As Double = 0, _
                                Optional penerimaantunai As Double = 0, _
                                Optional cnpenjualan As Double = 0, _
                                Optional pencairangiro As Double = 0, _
                                Optional penerimaanbersih As Double = 0, _
                                Optional targetpenerimaan As Double = 0, _
                                Optional prosenpenerimaan As Double = 0) As String
    Dim json As String
    
    json = "{"
    json = json & """tahun"":""" & Replace(tahun, """", "\""") & ""","
    json = json & """bulan"":""" & Replace(bulan, """", "\""") & ""","
    json = json & """kodesales"":""" & Replace(kodesales, """", "\""") & ""","
    json = json & """namasales"":""" & Replace(namasales, """", "\""") & ""","
    json = json & """jumlahfaktur"":" & jumlahfaktur & ","
    json = json & """penjualan"":" & penjualan & ","
    json = json & """returpenjualan"":" & returpenjualan & ","
    json = json & """penjualanbersih"":" & penjualanbersih & ","
    json = json & """targetpenjualan"":" & targetpenjualan & ","
    json = json & """prosenpenjualan"":" & prosenpenjualan & ","
    json = json & """penerimaantunai"":" & penerimaantunai & ","
    json = json & """cnpenjualan"":" & cnpenjualan & ","
    json = json & """pencairangiro"":" & pencairangiro & ","
    json = json & """penerimaanbersih"":" & penerimaanbersih & ","
    json = json & """targetpenerimaan"":" & targetpenerimaan & ","
    json = json & """prosenpenerimaan"":" & prosenpenerimaan
    json = json & "}"
    
    BuildOmsetJSON = json
End Function

