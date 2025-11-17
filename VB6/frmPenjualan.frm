VERSION 5.00
Begin VB.Form frmPenjualan 
   Caption         =   "Penjualan (Bridging API)"
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
   Begin VB.CommandButton cmdDeletePenjualan 
      Caption         =   "Delete"
      Height          =   345
      Left            =   12540
      TabIndex        =   19
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdPatchPenjualan 
      Caption         =   "Patch (JSON)"
      Height          =   345
      Left            =   10860
      TabIndex        =   18
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdUpdatePenjualan 
      Caption         =   "Update (PUT)"
      Height          =   345
      Left            =   9180
      TabIndex        =   17
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdCreatePenjualan 
      Caption         =   "Create (POST)"
      Height          =   345
      Left            =   7500
      TabIndex        =   16
      Top             =   6720
      Width           =   1590
   End
   Begin VB.CommandButton cmdUpdateOrderInfo 
      Caption         =   "Update No Order"
      Height          =   345
      Left            =   10440
      TabIndex        =   15
      Top             =   6240
      Width           =   1905
   End
   Begin VB.CommandButton cmdUpdateSaldo 
      Caption         =   "Update Saldo"
      Height          =   345
      Left            =   9180
      TabIndex        =   14
      Top             =   6240
      Width           =   1110
   End
   Begin VB.TextBox txtUpdateTanggalOrder 
      Height          =   315
      Left            =   11940
      TabIndex        =   13
      Top             =   5880
      Width           =   1680
   End
   Begin VB.TextBox txtUpdateNoOrder 
      Height          =   315
      Left            =   10020
      TabIndex        =   12
      Top             =   5880
      Width           =   1875
   End
   Begin VB.TextBox txtUpdateSaldo 
      Height          =   315
      Left            =   9180
      TabIndex        =   11
      Top             =   5880
      Width           =   735
   End
   Begin VB.ListBox lstDetailPenjualan 
      Height          =   2115
      Left            =   6600
      TabIndex        =   10
      Top             =   3480
      Width           =   6870
   End
   Begin VB.TextBox txtDetailKeterangan 
      Height          =   765
      Left            =   6600
      MultiLine       =   -1  'True
      TabIndex        =   9
      Top             =   2640
      Width           =   3030
   End
   Begin VB.TextBox txtDetailNilai 
      Height          =   315
      Left            =   10440
      TabIndex        =   8
      Top             =   3060
      Width           =   1200
   End
   Begin VB.TextBox txtDetailPPN 
      Height          =   315
      Left            =   10440
      TabIndex        =   7
      Top             =   2640
      Width           =   1200
   End
   Begin VB.TextBox txtDetailDPP 
      Height          =   315
      Left            =   10440
      TabIndex        =   6
      Top             =   2220
      Width           =   1200
   End
   Begin VB.TextBox txtDetailSaldo 
      Height          =   315
      Left            =   10440
      TabIndex        =   5
      Top             =   1800
      Width           =   1200
   End
   Begin VB.TextBox txtDetailPengirim 
      Height          =   315
      Left            =   6600
      TabIndex        =   4
      Top             =   2220
      Width           =   3030
   End
   Begin VB.TextBox txtDetailSales 
      Height          =   315
      Left            =   10020
      TabIndex        =   3
      Top             =   1380
      Width           =   2250
   End
   Begin VB.TextBox txtDetailCustomer 
      Height          =   315
      Left            =   6600
      TabIndex        =   2
      Top             =   1380
      Width           =   3030
   End
   Begin VB.TextBox txtDetailTanggalOrder 
      Height          =   315
      Left            =   10020
      TabIndex        =   1
      Top             =   1800
      Width           =   2250
   End
   Begin VB.TextBox txtDetailNoOrder 
      Height          =   315
      Left            =   6600
      TabIndex        =   0
      Top             =   1800
      Width           =   3030
   End
   Begin VB.TextBox txtDetailTanggal 
      Height          =   315
      Left            =   10020
      TabIndex        =   24
      Top             =   960
      Width           =   2250
   End
   Begin VB.TextBox txtDetailNoPenjualan 
      Height          =   315
      Left            =   6600
      TabIndex        =   23
      Top             =   960
      Width           =   3030
   End
   Begin VB.ListBox lstPenjualan 
      Height          =   2730
      Left            =   240
      TabIndex        =   22
      Top             =   1320
      Width           =   6120
   End
   Begin VB.CommandButton cmdRefreshPenjualan 
      Caption         =   "Refresh"
      Height          =   345
      Left            =   5580
      TabIndex        =   21
      Top             =   840
      Width           =   945
   End
   Begin VB.CommandButton cmdLoadPenjualan 
      Caption         =   "Load"
      Height          =   345
      Left            =   4500
      TabIndex        =   25
      Top             =   840
      Width           =   945
   End
   Begin VB.ComboBox cboPenjualanPerPage 
      Height          =   315
      Left            =   3300
      Style           =   2  'Dropdown List
      TabIndex        =   26
      Top             =   840
      Width           =   975
   End
   Begin VB.TextBox txtEndDate 
      Height          =   315
      Left            =   2400
      TabIndex        =   27
      Top             =   840
      Width           =   900
   End
   Begin VB.TextBox txtStartDate 
      Height          =   315
      Left            =   1260
      TabIndex        =   28
      Top             =   840
      Width           =   900
   End
   Begin VB.ComboBox cboPeriode 
      Height          =   315
      Left            =   240
      Style           =   2  'Dropdown List
      TabIndex        =   29
      Top             =   840
      Width           =   900
   End
   Begin VB.TextBox txtSearchPenjualan 
      Height          =   315
      Left            =   240
      TabIndex        =   30
      Top             =   360
      Width           =   3285
   End
   Begin VB.Label lblPayloadInfo 
      Caption         =   "Masukkan payload JSON untuk create/update/patch:"
      Height          =   255
      Left            =   6600
      TabIndex        =   31
      Top             =   6360
      Width           =   6870
   End
   Begin VB.Label lblUpdateTanggalOrder 
      Caption         =   "Tanggal Order (YYYY-MM-DD):"
      Height          =   255
      Left            =   11940
      TabIndex        =   32
      Top             =   5640
      Width           =   1860
   End
   Begin VB.Label lblUpdateNoOrder 
      Caption         =   "No Order:"
      Height          =   255
      Left            =   10020
      TabIndex        =   33
      Top             =   5640
      Width           =   1200
   End
   Begin VB.Label lblUpdateSaldo 
      Caption         =   "Saldo Baru:"
      Height          =   255
      Left            =   9180
      TabIndex        =   34
      Top             =   5640
      Width           =   900
   End
   Begin VB.Label lblDetailList 
      Caption         =   "Detail Barang:"
      Height          =   255
      Left            =   6600
      TabIndex        =   35
      Top             =   3240
      Width           =   1815
   End
   Begin VB.Label lblDetailKeterangan 
      Caption         =   "Keterangan:"
      Height          =   255
      Left            =   6600
      TabIndex        =   36
      Top             =   2400
      Width           =   1815
   End
   Begin VB.Label lblDetailNilai 
      Caption         =   "Nilai Penjualan:"
      Height          =   255
      Left            =   10440
      TabIndex        =   37
      Top             =   2820
      Width           =   1455
   End
   Begin VB.Label lblDetailPPN 
      Caption         =   "PPN:"
      Height          =   255
      Left            =   10440
      TabIndex        =   38
      Top             =   2400
      Width           =   735
   End
   Begin VB.Label lblDetailDPP 
      Caption         =   "DPP:"
      Height          =   255
      Left            =   10440
      TabIndex        =   39
      Top             =   1980
      Width           =   735
   End
   Begin VB.Label lblDetailSaldo 
      Caption         =   "Saldo:"
      Height          =   255
      Left            =   10440
      TabIndex        =   40
      Top             =   1560
      Width           =   735
   End
   Begin VB.Label lblDetailPengirim 
      Caption         =   "Pengirim:"
      Height          =   255
      Left            =   6600
      TabIndex        =   41
      Top             =   1980
      Width           =   1455
   End
   Begin VB.Label lblDetailSales 
      Caption         =   "Sales:"
      Height          =   255
      Left            =   10020
      TabIndex        =   42
      Top             =   1140
      Width           =   1335
   End
   Begin VB.Label lblDetailCustomer 
      Caption         =   "Customer:"
      Height          =   255
      Left            =   6600
      TabIndex        =   43
      Top             =   1140
      Width           =   1455
   End
   Begin VB.Label lblDetailTanggalOrder 
      Caption         =   "Tanggal Order:"
      Height          =   255
      Left            =   10020
      TabIndex        =   44
      Top             =   1560
      Width           =   1455
   End
  Begin VB.Label lblDetailNoOrder 
      Caption         =   "No Order:"
      Height          =   255
      Left            =   6600
      TabIndex        =   45
      Top             =   1560
      Width           =   1455
   End
   Begin VB.Label lblDetailTanggal 
      Caption         =   "Tanggal Penjualan:"
      Height          =   255
      Left            =   10020
      TabIndex        =   46
      Top             =   720
      Width           =   1695
   End
   Begin VB.Label lblDetailNoPenjualan 
      Caption         =   "No Penjualan:"
      Height          =   255
      Left            =   6600
      TabIndex        =   47
      Top             =   720
      Width           =   1455
   End
   Begin VB.Label lblPenjualanList 
      Caption         =   "Daftar Penjualan:"
      Height          =   255
      Left            =   240
      TabIndex        =   48
      Top             =   1080
      Width           =   2055
   End
   Begin VB.Label lblPerPage 
      Caption         =   "Per Page:"
      Height          =   255
      Left            =   3300
      TabIndex        =   49
      Top             =   600
      Width           =   855
   End
   Begin VB.Label lblEndDate 
      Caption         =   "End Date:"
      Height          =   255
      Left            =   2400
      TabIndex        =   50
      Top             =   600
      Width           =   855
   End
   Begin VB.Label lblStartDate 
      Caption         =   "Start Date:"
      Height          =   255
      Left            =   1260
      TabIndex        =   51
      Top             =   600
      Width           =   915
   End
   Begin VB.Label lblPeriode 
      Caption         =   "Periode:"
      Height          =   255
      Left            =   240
      TabIndex        =   52
      Top             =   600
      Width           =   855
   End
   Begin VB.Label lblSearch 
      Caption         =   "Pencarian (No/Customer/Sales):"
      Height          =   255
      Left            =   240
      TabIndex        =   53
      Top             =   120
      Width           =   2895
   End
   Begin VB.Label lblPenjualanStatus 
      Caption         =   "Status: Ready"
      Height          =   255
      Left            =   240
      TabIndex        =   54
      Top             =   9360
      Width           =   12960
   End
End
Attribute VB_Name = "frmPenjualan"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Form_Load()
    cboPeriode.Clear
    cboPeriode.AddItem "today"
    cboPeriode.AddItem "week"
    cboPeriode.AddItem "month"
    cboPeriode.AddItem "year"
    cboPeriode.AddItem "custom"
    cboPeriode.ListIndex = 0

    cboPenjualanPerPage.Clear
    cboPenjualanPerPage.AddItem "10"
    cboPenjualanPerPage.AddItem "20"
    cboPenjualanPerPage.AddItem "50"
    cboPenjualanPerPage.AddItem "75"
    cboPenjualanPerPage.AddItem "100"
    cboPenjualanPerPage.AddItem "200"
    cboPenjualanPerPage.AddItem "500"
    cboPenjualanPerPage.ListIndex = 1 'default 20

    txtStartDate.Enabled = False
    txtEndDate.Enabled = False

    txtPayload.Text = "{""nopenjualan"":""PNJ240001"",""tanggalpenjualan"":""2025-01-10"",""statuspkp"":""pkp"",""kodeformulir"":""F001"",""kodecustomer"":""C001"",""kodesales"":""S001"",""dpp"":""1000000"",""ppn"":""110000"",""nilaipenjualan"":""1110000"",""saldopenjualan"":""1110000"",""userid"":""admin"",""details"":[{""kodebarang"":""BRG001"",""jumlah"":10,""hargasatuan"":""100000"",""jumlahharga"":""1000000"",""discount"":0}]}"

    lblPenjualanStatus.Caption = "Status: Ready"
End Sub

Private Sub cboPeriode_Click()
    UpdateCustomDateState
End Sub

Private Sub cboPeriode_Change()
    UpdateCustomDateState
End Sub

Private Sub cmdLoadPenjualan_Click()
    LoadPenjualanList
End Sub

Private Sub cmdRefreshPenjualan_Click()
    LoadPenjualanList
End Sub

Private Sub lstPenjualan_Click()
    Dim nomor As String
    nomor = GetSelectedPenjualanNo()
    If nomor <> "" Then
        ShowPenjualanDetail nomor
    End If
End Sub

Private Sub cmdUpdateSaldo_Click()
    Dim nomor As String
    Dim saldoBaru As String
    Dim response As String

    nomor = GetSelectedPenjualanNo()
    If nomor = "" Then
        MsgBox "Silakan pilih penjualan terlebih dahulu.", vbExclamation
        Exit Sub
    End If

    saldoBaru = Trim$(txtUpdateSaldo.Text)
    If saldoBaru = "" Then
        MsgBox "Masukkan nilai saldo baru.", vbExclamation
        Exit Sub
    End If

    response = UpdatePenjualanSaldo(nomor, saldoBaru)
    HandlePenjualanResponse response, "Saldo penjualan diperbarui.", False, nomor
End Sub

Private Sub cmdUpdateOrderInfo_Click()
    Dim nomor As String
    Dim noOrderBaru As String
    Dim tanggalOrderBaru As String
    Dim response As String

    nomor = GetSelectedPenjualanNo()
    If nomor = "" Then
        MsgBox "Silakan pilih penjualan terlebih dahulu.", vbExclamation
        Exit Sub
    End If

    noOrderBaru = Trim$(txtUpdateNoOrder.Text)
    tanggalOrderBaru = Trim$(txtUpdateTanggalOrder.Text)

    If noOrderBaru = "" Or tanggalOrderBaru = "" Then
        MsgBox "Masukkan No Order dan Tanggal Order (format YYYY-MM-DD).", vbExclamation
        Exit Sub
    End If

    response = UpdatePenjualanOrderInfo(nomor, noOrderBaru, tanggalOrderBaru)
    HandlePenjualanResponse response, "Informasi order diperbarui.", False, nomor
End Sub

Private Sub cmdCreatePenjualan_Click()
    Dim payload As String
    Dim response As String

    payload = Trim$(txtPayload.Text)
    If payload = "" Then
        MsgBox "Masukkan payload JSON untuk create.", vbExclamation
        Exit Sub
    End If

    response = CreatePenjualan(payload)
    HandlePenjualanResponse response, "Penjualan berhasil dibuat.", True
End Sub

Private Sub cmdUpdatePenjualan_Click()
    Dim payload As String
    Dim response As String

    payload = Trim$(txtPayload.Text)
    If payload = "" Then
        MsgBox "Masukkan payload JSON untuk update PUT.", vbExclamation
        Exit Sub
    End If

    response = UpdatePenjualan(payload)
    HandlePenjualanResponse response, "Penjualan berhasil diperbarui.", True
End Sub

Private Sub cmdPatchPenjualan_Click()
    Dim payload As String
    Dim response As String

    payload = Trim$(txtPayload.Text)
    If payload = "" Then
        MsgBox "Masukkan payload JSON untuk patch.", vbExclamation
        Exit Sub
    End If

    response = PatchPenjualan(payload)
    HandlePenjualanResponse response, "Penjualan berhasil di-patch.", True
End Sub

Private Sub cmdDeletePenjualan_Click()
    Dim nomor As String
    Dim response As String

    nomor = GetSelectedPenjualanNo()
    If nomor = "" Then
        MsgBox "Silakan pilih penjualan yang akan dihapus.", vbExclamation
        Exit Sub
    End If

    If MsgBox("Hapus penjualan " & nomor & "?", vbQuestion + vbYesNo) = vbYes Then
        response = DeletePenjualan(nomor)
        HandlePenjualanResponse response, "Penjualan berhasil dihapus.", True
    End If
End Sub

Private Sub LoadPenjualanList(Optional ByVal selectedNo As String = "")
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
    Dim displayText As String
    Dim periodeValue As String
    Dim perPageValue As Long
    Dim i As Long

    On Error GoTo ErrorHandler

    lblPenjualanStatus.Caption = "Memuat data penjualan..."
    lstPenjualan.Clear

    perPageValue = CLng(Val(cboPenjualanPerPage.Text))
    If perPageValue <= 0 Then perPageValue = 20
    periodeValue = Trim$(cboPeriode.Text)

    response = GetPenjualan(1, perPageValue, Trim$(txtSearchPenjualan.Text), periodeValue, Trim$(txtStartDate.Text), Trim$(txtEndDate.Text))

    If Left$(response, 5) = "ERROR" Then
        lblPenjualanStatus.Caption = "Error: " & response
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")
    If LCase$(Trim$(success)) <> "true" Then
        lblPenjualanStatus.Caption = "Error: " & GetAPIErrorMessage(response)
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

            nomor = ParseJSONValue(item, "nopenjualan")
            customer = ParseJSONValue(item, "namacustomer")
            tanggal = ParseJSONValue(item, "tanggalpenjualan")
            sales = ParseJSONValue(item, "namasales")

            If nomor <> "" Then
                displayText = nomor
                If tanggal <> "" Then displayText = displayText & " - " & FormatDateString(tanggal)
                If customer <> "" Then displayText = displayText & " - " & customer
                If sales <> "" Then displayText = displayText & " - " & sales
                lstPenjualan.AddItem displayText
            End If

            startPos = InStr(endPos + 1, data, "{")
        Loop
    End If

    lblPenjualanStatus.Caption = "Memuat " & lstPenjualan.ListCount & " data penjualan (Total: " & totalRecords & ")."

    If lstPenjualan.ListCount > 0 Then
        If selectedNo <> "" Then
            For i = 0 To lstPenjualan.ListCount - 1
                If Left$(lstPenjualan.List(i), Len(selectedNo)) = selectedNo Then
                    lstPenjualan.ListIndex = i
                    Exit For
                End If
            Next i
        End If

        If lstPenjualan.ListIndex < 0 Then
            lstPenjualan.ListIndex = 0
        End If
    End If

    Exit Sub

ErrorHandler:
    lblPenjualanStatus.Caption = "Error: " & Err.Description
End Sub

Private Sub ShowPenjualanDetail(ByVal nopenjualan As String)
    Dim response As String
    Dim success As String
    Dim data As String
    Dim detailList As String
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    Dim kodebarang As String
    Dim namabarang As String
    Dim jumlah As String
    Dim harga As String
    Dim total As String
    Dim batch As String
    Dim expDate As String

    response = GetPenjualanByNo(nopenjualan)

    If Left$(response, 5) = "ERROR" Then
        lblPenjualanStatus.Caption = response
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")
    If LCase$(Trim$(success)) <> "true" Then
        lblPenjualanStatus.Caption = "Error: " & GetAPIErrorMessage(response)
        Exit Sub
    End If

    data = ParseJSONValue(response, "data")

    Dim statuspkp As String
    statuspkp = ParseJSONValue(data, "statuspkp")
    
    txtDetailNoPenjualan.Text = ParseJSONValue(data, "nopenjualan")
    If statuspkp <> "" Then
        If LCase$(Trim$(statuspkp)) = "pkp" Then
            txtDetailNoPenjualan.Text = txtDetailNoPenjualan.Text & " [PKP]"
        ElseIf LCase$(Trim$(statuspkp)) = "nonpkp" Then
            txtDetailNoPenjualan.Text = txtDetailNoPenjualan.Text & " [Non PKP]"
        End If
    End If
    
    txtDetailTanggal.Text = FormatDateString(ParseJSONValue(data, "tanggalpenjualan"))
    txtDetailCustomer.Text = ParseJSONValue(data, "namacustomer")
    txtDetailSales.Text = ParseJSONValue(data, "namasales")
    txtDetailNoOrder.Text = ParseJSONValue(data, "noorder")
    txtDetailTanggalOrder.Text = FormatDateString(ParseJSONValue(data, "tanggalorder"))
    txtDetailPengirim.Text = ParseJSONValue(data, "pengirim")
    txtDetailSaldo.Text = FormatCurrencyString(ParseJSONValue(data, "saldopenjualan"))
    txtDetailDPP.Text = FormatCurrencyString(ParseJSONValue(data, "dpp"))
    txtDetailPPN.Text = FormatCurrencyString(ParseJSONValue(data, "ppn"))
    txtDetailNilai.Text = FormatCurrencyString(ParseJSONValue(data, "nilaipenjualan"))
    txtDetailKeterangan.Text = ParseJSONValue(data, "keterangan")

    detailList = ParseJSONValue(data, "details")
    lstDetailPenjualan.Clear

    If Left$(detailList, 1) = "[" Then
        startPos = InStr(2, detailList, "{")
        Do While startPos > 0 And startPos < Len(detailList)
            endPos = InStr(startPos, detailList, "}")
            If endPos = 0 Then Exit Do

            item = Mid$(detailList, startPos, endPos - startPos + 1)
            kodebarang = ParseJSONValue(item, "kodebarang")
            namabarang = ParseJSONValue(item, "namabarang")
            jumlah = ParseJSONValue(item, "jumlah")
            harga = ParseJSONValue(item, "hargasatuan")
            total = ParseJSONValue(item, "jumlahharga")
            batch = ParseJSONValue(item, "nomorbatch")
            expDate = ParseJSONValue(item, "expireddate")

            Dim lineText As String
            lineText = kodebarang
            If namabarang <> "" Then lineText = lineText & " - " & namabarang
            lineText = lineText & " | Qty: " & jumlah & " | Harga: " & harga & " | Total: " & total
            If batch <> "" Then lineText = lineText & " | Batch: " & batch
            If expDate <> "" Then lineText = lineText & " | Exp: " & expDate
            lstDetailPenjualan.AddItem lineText

            startPos = InStr(endPos + 1, detailList, "{")
        Loop
    End If

    lblPenjualanStatus.Caption = "Detail penjualan " & nopenjualan & " ditampilkan."
End Sub

Private Sub HandlePenjualanResponse(ByVal response As String, ByVal successMessage As String, Optional ByVal refreshList As Boolean = True, Optional ByVal preferredNo As String = "")
    Dim success As String
    Dim data As String
    Dim targetNo As String

    If Left$(response, 5) = "ERROR" Then
        lblPenjualanStatus.Caption = response
        MsgBox response, vbCritical
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")
    If LCase$(Trim$(success)) <> "true" Then
        lblPenjualanStatus.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
        Exit Sub
    End If

    If preferredNo <> "" Then
        targetNo = preferredNo
    Else
        data = ParseJSONValue(response, "data")
        targetNo = ParseJSONValue(data, "nopenjualan")
        If targetNo = "" Then
            targetNo = GetSelectedPenjualanNo()
        End If
    End If

    lblPenjualanStatus.Caption = successMessage

    If refreshList Then
        LoadPenjualanList targetNo
    Else
        If targetNo <> "" Then
            ShowPenjualanDetail targetNo
        End If
    End If
End Sub

Private Function GetSelectedPenjualanNo() As String
    Dim displayText As String
    Dim parts() As String

    If lstPenjualan.ListIndex < 0 Then Exit Function

    displayText = lstPenjualan.List(lstPenjualan.ListIndex)
    parts = Split(displayText, " - ")
    If UBound(parts) >= 0 Then
        GetSelectedPenjualanNo = Trim$(parts(0))
    End If
End Function

Private Sub UpdateCustomDateState()
    Dim isCustom As Boolean
    isCustom = (LCase$(Trim$(cboPeriode.Text)) = "custom")

    txtStartDate.Enabled = isCustom
    txtEndDate.Enabled = isCustom
    If Not isCustom Then
        txtStartDate.Text = ""
        txtEndDate.Text = ""
    End If
End Sub

Private Function FormatCurrencyString(ByVal value As String) As String
    On Error GoTo Fallback
    If Trim$(value) = "" Then
        FormatCurrencyString = "0"
    Else
        FormatCurrencyString = FormatNumber(CDbl(Val(value)), 2, vbTrue, vbFalse, vbTrue)
    End If
    Exit Function
Fallback:
    FormatCurrencyString = value
End Function

Private Function FormatDateString(ByVal value As String) As String
    On Error GoTo Fallback
    If Trim$(value) = "" Then
        FormatDateString = ""
    Else
        FormatDateString = Format$(CDate(value), "dd/mm/yyyy")
    End If
    Exit Function
Fallback:
    FormatDateString = value
End Function




