VERSION 5.00
Begin VB.Form frmPenerimaan 
   Caption         =   "Penerimaan Piutang (Bridging API)"
   ClientHeight    =   9720
   ClientLeft      =   120
   ClientTop       =   465
   ClientWidth     =   13695
   LinkTopic       =   "Form1"
   ScaleHeight     =   9720
   ScaleWidth      =   13695
   StartUpPosition =   3  'Windows Default
   Begin VB.TextBox txtPayload 
      Height          =   2175
      Left            =   6600
      MultiLine       =   -1  'True
      ScrollBars      =   2  'Vertical
      TabIndex        =   20
      Top             =   7080
      Width           =   6870
   End
   Begin VB.CommandButton cmdDeletePenerimaan 
      Caption         =   "Delete"
      Height          =   345
      Left            =   12540
      TabIndex        =   19
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdPatchPenerimaan 
      Caption         =   "Patch (JSON)"
      Height          =   345
      Left            =   10860
      TabIndex        =   18
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdUpdatePenerimaan 
      Caption         =   "Update (PUT)"
      Height          =   345
      Left            =   9180
      TabIndex        =   17
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdCreatePenerimaan 
      Caption         =   "Create (POST)"
      Height          =   345
      Left            =   7500
      TabIndex        =   16
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdUpdateStatus 
      Caption         =   "Update Status"
      Height          =   345
      Left            =   9180
      TabIndex        =   15
      Top             =   6240
      Width           =   1905
   End
   Begin VB.TextBox txtUpdateNoInkaso 
      Height          =   315
      Left            =   11940
      TabIndex        =   14
      Top             =   5880
      Width           =   1680
   End
   Begin VB.ComboBox cboUpdateStatus 
      Height          =   315
      Left            =   10020
      Style           =   2  'Dropdown List
      TabIndex        =   13
      Top             =   5880
      Width           =   1875
   End
   Begin VB.ListBox lstDetailPenerimaan 
      Height          =   2115
      Left            =   6600
      TabIndex        =   12
      Top             =   3480
      Width           =   6870
   End
   Begin VB.TextBox txtDetailNoInkaso 
      Height          =   315
      Left            =   10440
      TabIndex        =   11
      Top             =   3060
      Width           =   1200
   End
   Begin VB.TextBox txtDetailStatus 
      Height          =   315
      Left            =   10440
      TabIndex        =   10
      Top             =   2640
      Width           =   1200
   End
   Begin VB.TextBox txtDetailTotalNetto 
      Height          =   315
      Left            =   10440
      TabIndex        =   9
      Top             =   2220
      Width           =   1200
   End
   Begin VB.TextBox txtDetailTotalLainlain 
      Height          =   315
      Left            =   10440
      TabIndex        =   8
      Top             =   1800
      Width           =   1200
   End
   Begin VB.TextBox txtDetailTotalPotongan 
      Height          =   315
      Left            =   6600
      TabIndex        =   7
      Top             =   3060
      Width           =   3030
   End
   Begin VB.TextBox txtDetailTotalPiutang 
      Height          =   315
      Left            =   6600
      TabIndex        =   6
      Top             =   2640
      Width           =   3030
   End
   Begin VB.TextBox txtDetailJenisPenerimaan 
      Height          =   315
      Left            =   6600
      TabIndex        =   5
      Top             =   2220
      Width           =   3030
   End
   Begin VB.TextBox txtDetailSales 
      Height          =   315
      Left            =   10020
      TabIndex        =   4
      Top             =   1380
      Width           =   2250
   End
   Begin VB.TextBox txtDetailCustomer 
      Height          =   315
      Left            =   6600
      TabIndex        =   3
      Top             =   1380
      Width           =   3030
   End
   Begin VB.TextBox txtDetailTanggal 
      Height          =   315
      Left            =   10020
      TabIndex        =   2
      Top             =   960
      Width           =   2250
   End
   Begin VB.TextBox txtDetailNoPenerimaan 
      Height          =   315
      Left            =   6600
      TabIndex        =   1
      Top             =   960
      Width           =   3030
   End
   Begin VB.ListBox lstPenerimaan 
      Height          =   2730
      Left            =   240
      TabIndex        =   0
      Top             =   1320
      Width           =   6120
   End
   Begin VB.CommandButton cmdRefreshPenerimaan 
      Caption         =   "Refresh"
      Height          =   345
      Left            =   5580
      TabIndex        =   21
      Top             =   840
      Width           =   945
   End
   Begin VB.CommandButton cmdLoadPenerimaan 
      Caption         =   "Load"
      Height          =   345
      Left            =   4500
      TabIndex        =   22
      Top             =   840
      Width           =   945
   End
   Begin VB.ComboBox cboPenerimaanPerPage 
      Height          =   315
      Left            =   3300
      Style           =   2  'Dropdown List
      TabIndex        =   23
      Top             =   840
      Width           =   975
   End
   Begin VB.TextBox txtEndDate 
      Height          =   315
      Left            =   2400
      TabIndex        =   24
      Top             =   840
      Width           =   900
   End
   Begin VB.TextBox txtStartDate 
      Height          =   315
      Left            =   1260
      TabIndex        =   25
      Top             =   840
      Width           =   900
   End
   Begin VB.ComboBox cboStatusFilter 
      Height          =   315
      Left            =   240
      Style           =   2  'Dropdown List
      TabIndex        =   26
      Top             =   840
      Width           =   900
   End
   Begin VB.TextBox txtSearchPenerimaan 
      Height          =   315
      Left            =   240
      TabIndex        =   27
      Top             =   360
      Width           =   3285
   End
   Begin VB.Label lblPayloadInfo 
      Caption         =   "Masukkan payload JSON untuk create/update/patch:"
      Height          =   255
      Left            =   6600
      TabIndex        =   28
      Top             =   6360
      Width           =   6870
   End
   Begin VB.Label lblUpdateNoInkaso 
      Caption         =   "No Inkaso:"
      Height          =   255
      Left            =   11940
      TabIndex        =   29
      Top             =   5640
      Width           =   1680
   End
   Begin VB.Label lblUpdateStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   10020
      TabIndex        =   30
      Top             =   5640
      Width           =   1200
   End
   Begin VB.Label lblDetailList 
      Caption         =   "Detail Penerimaan:"
      Height          =   255
      Left            =   6600
      TabIndex        =   31
      Top             =   3240
      Width           =   1815
   End
   Begin VB.Label lblDetailNoInkaso 
      Caption         =   "No Inkaso:"
      Height          =   255
      Left            =   10440
      TabIndex        =   32
      Top             =   2820
      Width           =   1200
   End
   Begin VB.Label lblDetailStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   10440
      TabIndex        =   33
      Top             =   2400
      Width           =   1200
   End
   Begin VB.Label lblDetailTotalNetto 
      Caption         =   "Total Netto:"
      Height          =   255
      Left            =   10440
      TabIndex        =   34
      Top             =   1980
      Width           =   1200
   End
   Begin VB.Label lblDetailTotalLainlain 
      Caption         =   "Total Lain-lain:"
      Height          =   255
      Left            =   10440
      TabIndex        =   35
      Top             =   1560
      Width           =   1200
   End
   Begin VB.Label lblDetailTotalPotongan 
      Caption         =   "Total Potongan:"
      Height          =   255
      Left            =   6600
      TabIndex        =   36
      Top             =   2820
      Width           =   1815
   End
   Begin VB.Label lblDetailTotalPiutang 
      Caption         =   "Total Piutang:"
      Height          =   255
      Left            =   6600
      TabIndex        =   37
      Top             =   2400
      Width           =   1815
   End
   Begin VB.Label lblDetailJenisPenerimaan 
      Caption         =   "Jenis Penerimaan:"
      Height          =   255
      Left            =   6600
      TabIndex        =   38
      Top             =   1980
      Width           =   1815
   End
   Begin VB.Label lblDetailSales 
      Caption         =   "Sales:"
      Height          =   255
      Left            =   10020
      TabIndex        =   39
      Top             =   1140
      Width           =   1335
   End
   Begin VB.Label lblDetailCustomer 
      Caption         =   "Customer:"
      Height          =   255
      Left            =   6600
      TabIndex        =   40
      Top             =   1140
      Width           =   1455
   End
   Begin VB.Label lblDetailTanggal 
      Caption         =   "Tanggal Penerimaan:"
      Height          =   255
      Left            =   10020
      TabIndex        =   41
      Top             =   720
      Width           =   1695
   End
   Begin VB.Label lblDetailNoPenerimaan 
      Caption         =   "No Penerimaan:"
      Height          =   255
      Left            =   6600
      TabIndex        =   42
      Top             =   720
      Width           =   1455
   End
   Begin VB.Label lblPenerimaanList 
      Caption         =   "Daftar Penerimaan:"
      Height          =   255
      Left            =   240
      TabIndex        =   43
      Top             =   1080
      Width           =   2055
   End
   Begin VB.Label lblPerPage 
      Caption         =   "Per Page:"
      Height          =   255
      Left            =   3300
      TabIndex        =   44
      Top             =   600
      Width           =   855
   End
   Begin VB.Label lblEndDate 
      Caption         =   "End Date:"
      Height          =   255
      Left            =   2400
      TabIndex        =   45
      Top             =   600
      Width           =   855
   End
   Begin VB.Label lblStartDate 
      Caption         =   "Start Date:"
      Height          =   255
      Left            =   1260
      TabIndex        =   46
      Top             =   600
      Width           =   915
   End
   Begin VB.Label lblStatusFilter 
      Caption         =   "Status:"
      Height          =   255
      Left            =   240
      TabIndex        =   47
      Top             =   600
      Width           =   855
   End
   Begin VB.Label lblSearch 
      Caption         =   "Pencarian (No/Customer/Sales/Inkaso):"
      Height          =   255
      Left            =   240
      TabIndex        =   48
      Top             =   120
      Width           =   2895
   End
   Begin VB.Label lblPenerimaanStatus 
      Caption         =   "Status: Ready"
      Height          =   255
      Left            =   240
      TabIndex        =   49
      Top             =   9360
      Width           =   12960
   End
End
Attribute VB_Name = "frmPenerimaan"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Form_Load()
    cboStatusFilter.Clear
    cboStatusFilter.AddItem ""
    cboStatusFilter.AddItem "belumproses"
    cboStatusFilter.AddItem "proses"
    cboStatusFilter.ListIndex = 0

    cboPenerimaanPerPage.Clear
    cboPenerimaanPerPage.AddItem "10"
    cboPenerimaanPerPage.AddItem "20"
    cboPenerimaanPerPage.AddItem "50"
    cboPenerimaanPerPage.AddItem "75"
    cboPenerimaanPerPage.AddItem "100"
    cboPenerimaanPerPage.AddItem "200"
    cboPenerimaanPerPage.AddItem "500"
    cboPenerimaanPerPage.ListIndex = 1 'default 20

    cboUpdateStatus.Clear
    cboUpdateStatus.AddItem "belumproses"
    cboUpdateStatus.AddItem "proses"
    cboUpdateStatus.ListIndex = 0

    txtStartDate.Enabled = True
    txtEndDate.Enabled = True

    txtPayload.Text = "{""nopenerimaan"":""PNR240001"",""tanggalpenerimaan"":""2025-01-10"",""statuspkp"":""pkp"",""jenispenerimaan"":""tunai"",""kodesales"":""S001"",""kodecustomer"":""C001"",""totalpiutang"":""1000000"",""totalpotongan"":""0"",""totallainlain"":""0"",""totalnetto"":""1000000"",""status"":""belumproses"",""details"":[{""nopenjualan"":""PNJ240001"",""piutang"":""1000000"",""potongan"":""0"",""lainlain"":""0"",""netto"":""1000000""}]}"

    lblPenerimaanStatus.Caption = "Status: Ready"
End Sub

Private Sub cmdLoadPenerimaan_Click()
    LoadPenerimaanList
End Sub

Private Sub cmdRefreshPenerimaan_Click()
    LoadPenerimaanList
End Sub

Private Sub lstPenerimaan_Click()
    Dim nomor As String
    nomor = GetSelectedPenerimaanNo()
    If nomor <> "" Then
        ShowPenerimaanDetail nomor
    End If
End Sub

Private Sub cmdUpdateStatus_Click()
    Dim nomor As String
    Dim statusBaru As String
    Dim noInkasoBaru As String
    Dim response As String

    nomor = GetSelectedPenerimaanNo()
    If nomor = "" Then
        MsgBox "Silakan pilih penerimaan terlebih dahulu.", vbExclamation
        Exit Sub
    End If

    statusBaru = Trim$(cboUpdateStatus.Text)
    noInkasoBaru = Trim$(txtUpdateNoInkaso.Text)

    If statusBaru = "" Then
        MsgBox "Pilih status baru.", vbExclamation
        Exit Sub
    End If

    response = UpdatePenerimaanStatus(nomor, statusBaru, noInkasoBaru)
    HandlePenerimaanResponse response, "Status penerimaan diperbarui.", False, nomor
End Sub

Private Sub cmdCreatePenerimaan_Click()
    Dim payload As String
    Dim response As String

    payload = Trim$(txtPayload.Text)
    If payload = "" Then
        MsgBox "Masukkan payload JSON untuk create.", vbExclamation
        Exit Sub
    End If

    response = CreatePenerimaan(payload)
    HandlePenerimaanResponse response, "Penerimaan berhasil dibuat.", True
End Sub

Private Sub cmdUpdatePenerimaan_Click()
    Dim payload As String
    Dim response As String

    payload = Trim$(txtPayload.Text)
    If payload = "" Then
        MsgBox "Masukkan payload JSON untuk update PUT.", vbExclamation
        Exit Sub
    End If

    response = UpdatePenerimaan(payload)
    HandlePenerimaanResponse response, "Penerimaan berhasil diperbarui.", True
End Sub

Private Sub cmdPatchPenerimaan_Click()
    Dim payload As String
    Dim response As String

    payload = Trim$(txtPayload.Text)
    If payload = "" Then
        MsgBox "Masukkan payload JSON untuk patch.", vbExclamation
        Exit Sub
    End If

    response = PatchPenerimaan(payload)
    HandlePenerimaanResponse response, "Penerimaan berhasil di-patch.", True
End Sub

Private Sub cmdDeletePenerimaan_Click()
    Dim nomor As String
    Dim response As String

    nomor = GetSelectedPenerimaanNo()
    If nomor = "" Then
        MsgBox "Silakan pilih penerimaan yang akan dihapus.", vbExclamation
        Exit Sub
    End If

    If MsgBox("Hapus penerimaan " & nomor & "?", vbQuestion + vbYesNo) = vbYes Then
        response = DeletePenerimaan(nomor)
        HandlePenerimaanResponse response, "Penerimaan berhasil dihapus.", True
    End If
End Sub

Private Sub LoadPenerimaanList(Optional ByVal selectedNo As String = "")
    Dim response As String
    Dim success As String
    Dim data As String
    Dim pagination As String
    Dim totalRecords As String
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    Dim nomor As String
    Dim customer As String
    Dim tanggal As String
    Dim sales As String
    Dim status As String
    Dim displayText As String
    Dim statusFilterValue As String
    Dim perPageValue As Long
    Dim i As Long

    On Error GoTo ErrorHandler

    lblPenerimaanStatus.Caption = "Memuat data penerimaan..."
    lstPenerimaan.Clear

    perPageValue = CLng(Val(cboPenerimaanPerPage.Text))
    If perPageValue <= 0 Then perPageValue = 20
    statusFilterValue = Trim$(cboStatusFilter.Text)

    response = GetPenerimaan(1, perPageValue, Trim$(txtSearchPenerimaan.Text), statusFilterValue, "", "", Trim$(txtStartDate.Text), Trim$(txtEndDate.Text))

    If Left$(response, 5) = "ERROR" Then
        lblPenerimaanStatus.Caption = "Error: " & response
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")
    If LCase$(Trim$(success)) <> "true" Then
        lblPenerimaanStatus.Caption = "Error: " & GetAPIErrorMessage(response)
        Exit Sub
    End If

    data = ParseJSONValue(response, "data")
    pagination = ParseJSONValue(response, "pagination")
    totalRecords = ParseJSONValue(pagination, "total")

    If Left$(data, 1) = "[" Then
        startPos = InStr(2, data, "{")
        Do While startPos > 0 And startPos < Len(data)
            endPos = InStr(startPos, data, "}")
            If endPos = 0 Then Exit Do
            item = Mid$(data, startPos, endPos - startPos + 1)

            nomor = ParseJSONValue(item, "nopenerimaan")
            customer = ParseJSONValue(item, "namacustomer")
            tanggal = ParseJSONValue(item, "tanggalpenerimaan")
            sales = ParseJSONValue(item, "namasales")
            status = ParseJSONValue(item, "status")

            If nomor <> "" Then
                displayText = nomor
                If tanggal <> "" Then displayText = displayText & " - " & FormatDateString(tanggal)
                If customer <> "" Then displayText = displayText & " - " & customer
                If sales <> "" Then displayText = displayText & " - " & sales
                If status <> "" Then displayText = displayText & " [" & status & "]"
                lstPenerimaan.AddItem displayText
            End If

            startPos = InStr(endPos + 1, data, "{")
        Loop
    End If

    lblPenerimaanStatus.Caption = "Memuat " & lstPenerimaan.ListCount & " data penerimaan (Total: " & totalRecords & ")."

    If lstPenerimaan.ListCount > 0 Then
        If selectedNo <> "" Then
            For i = 0 To lstPenerimaan.ListCount - 1
                If Left$(lstPenerimaan.List(i), Len(selectedNo)) = selectedNo Then
                    lstPenerimaan.ListIndex = i
                    Exit For
                End If
            Next i
        End If

        If lstPenerimaan.ListIndex < 0 Then
            lstPenerimaan.ListIndex = 0
        End If
    End If

    Exit Sub

ErrorHandler:
    lblPenerimaanStatus.Caption = "Error: " & Err.Description
End Sub

Private Sub ShowPenerimaanDetail(ByVal nopenerimaan As String)
    Dim response As String
    Dim success As String
    Dim data As String
    Dim detailList As String
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    Dim nopenjualan As String
    Dim nogiro As String
    Dim tanggalcair As String
    Dim piutang As String
    Dim potongan As String
    Dim lainlain As String
    Dim netto As String

    response = GetPenerimaanByNo(nopenerimaan)

    If Left$(response, 5) = "ERROR" Then
        lblPenerimaanStatus.Caption = response
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")
    If LCase$(Trim$(success)) <> "true" Then
        lblPenerimaanStatus.Caption = "Error: " & GetAPIErrorMessage(response)
        Exit Sub
    End If

    data = ParseJSONValue(response, "data")

    Dim statuspkp As String
    statuspkp = ParseJSONValue(data, "statuspkp")
    
    txtDetailNoPenerimaan.Text = ParseJSONValue(data, "nopenerimaan")
    If statuspkp <> "" Then
        If LCase$(Trim$(statuspkp)) = "pkp" Then
            txtDetailNoPenerimaan.Text = txtDetailNoPenerimaan.Text & " [PKP]"
        ElseIf LCase$(Trim$(statuspkp)) = "nonpkp" Then
            txtDetailNoPenerimaan.Text = txtDetailNoPenerimaan.Text & " [Non PKP]"
        End If
    End If
    
    txtDetailTanggal.Text = FormatDateString(ParseJSONValue(data, "tanggalpenerimaan"))
    txtDetailCustomer.Text = ParseJSONValue(data, "namacustomer")
    txtDetailSales.Text = ParseJSONValue(data, "namasales")
    txtDetailJenisPenerimaan.Text = ParseJSONValue(data, "jenispenerimaan")
    txtDetailTotalPiutang.Text = FormatCurrencyString(ParseJSONValue(data, "totalpiutang"))
    txtDetailTotalPotongan.Text = FormatCurrencyString(ParseJSONValue(data, "totalpotongan"))
    txtDetailTotalLainlain.Text = FormatCurrencyString(ParseJSONValue(data, "totallainlain"))
    txtDetailTotalNetto.Text = FormatCurrencyString(ParseJSONValue(data, "totalnetto"))
    txtDetailStatus.Text = ParseJSONValue(data, "status")
    txtDetailNoInkaso.Text = ParseJSONValue(data, "noinkaso")

    detailList = ParseJSONValue(data, "details")
    lstDetailPenerimaan.Clear

    If Left$(detailList, 1) = "[" Then
        startPos = InStr(2, detailList, "{")
        Do While startPos > 0 And startPos < Len(detailList)
            endPos = InStr(startPos, detailList, "}")
            If endPos = 0 Then Exit Do

            item = Mid$(detailList, startPos, endPos - startPos + 1)
            nopenjualan = ParseJSONValue(item, "nopenjualan")
            nogiro = ParseJSONValue(item, "nogiro")
            tanggalcair = ParseJSONValue(item, "tanggalcair")
            piutang = ParseJSONValue(item, "piutang")
            potongan = ParseJSONValue(item, "potongan")
            lainlain = ParseJSONValue(item, "lainlain")
            netto = ParseJSONValue(item, "netto")

            Dim lineText As String
            lineText = nopenjualan
            If nogiro <> "" Then lineText = lineText & " | Giro: " & nogiro
            If tanggalcair <> "" Then lineText = lineText & " | Cair: " & FormatDateString(tanggalcair)
            lineText = lineText & " | Piutang: " & FormatCurrencyString(piutang)
            lineText = lineText & " | Potongan: " & FormatCurrencyString(potongan)
            lineText = lineText & " | Lain-lain: " & FormatCurrencyString(lainlain)
            lineText = lineText & " | Netto: " & FormatCurrencyString(netto)
            lstDetailPenerimaan.AddItem lineText

            startPos = InStr(endPos + 1, detailList, "{")
        Loop
    End If

    lblPenerimaanStatus.Caption = "Detail penerimaan " & nopenerimaan & " ditampilkan."
End Sub

Private Sub HandlePenerimaanResponse(ByVal response As String, ByVal successMessage As String, Optional ByVal refreshList As Boolean = True, Optional ByVal preferredNo As String = "")
    Dim success As String
    Dim data As String
    Dim targetNo As String

    If Left$(response, 5) = "ERROR" Then
        lblPenerimaanStatus.Caption = response
        MsgBox response, vbCritical
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")
    If LCase$(Trim$(success)) <> "true" Then
        lblPenerimaanStatus.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
        Exit Sub
    End If

    data = ParseJSONValue(response, "data")
    If data <> "" Then
        targetNo = ParseJSONValue(data, "nopenerimaan")
        If targetNo = "" Then targetNo = preferredNo
    Else
        targetNo = preferredNo
    End If

    lblPenerimaanStatus.Caption = successMessage
    MsgBox successMessage, vbInformation

    If refreshList Then
        LoadPenerimaanList targetNo
        If targetNo <> "" Then
            ShowPenerimaanDetail targetNo
        End If
    Else
        If targetNo <> "" Then
            ShowPenerimaanDetail targetNo
        End If
    End If
End Sub

Private Function GetSelectedPenerimaanNo() As String
    Dim selectedText As String
    Dim spacePos As Long

    If lstPenerimaan.ListIndex < 0 Then
        GetSelectedPenerimaanNo = ""
        Exit Function
    End If

    selectedText = lstPenerimaan.List(lstPenerimaan.ListIndex)
    spacePos = InStr(selectedText, " ")
    If spacePos > 0 Then
        GetSelectedPenerimaanNo = Left$(selectedText, spacePos - 1)
    Else
        GetSelectedPenerimaanNo = selectedText
    End If
End Function

