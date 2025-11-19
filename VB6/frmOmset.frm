VERSION 5.00
Begin VB.Form frmOmset 
   Caption         =   "Omset Management (Bridging API)"
   ClientHeight    =   9720
   ClientLeft      =   120
   ClientTop       =   465
   ClientWidth     =   13695
   LinkTopic       =   "Form1"
   ScaleHeight     =   9720
   ScaleWidth      =   13695
   StartUpPosition =   3  'Windows Default
   Begin VB.TextBox txtResponse 
      Height          =   2175
      Left            =   240
      MultiLine       =   -1  'True
      ScrollBars      =   2  'Vertical
      TabIndex        =   50
      Top             =   7200
      Width           =   13215
   End
   Begin VB.CommandButton cmdDelete 
      Caption         =   "Delete"
      Height          =   375
      Left            =   12000
      TabIndex        =   49
      Top             =   6720
      Width           =   1215
   End
   Begin VB.CommandButton cmdAdd 
      Caption         =   "Add (POST)"
      Height          =   375
      Left            =   10680
      TabIndex        =   48
      Top             =   6720
      Width           =   1215
   End
   Begin VB.CommandButton cmdFind 
      Caption         =   "Find (GET)"
      Height          =   375
      Left            =   9360
      TabIndex        =   47
      Top             =   6720
      Width           =   1215
   End
   Begin VB.TextBox txtProsenPenerimaan 
      Height          =   315
      Left            =   7200
      TabIndex        =   46
      Top             =   6240
      Width           =   1935
   End
   Begin VB.TextBox txtTargetPenerimaan 
      Height          =   315
      Left            =   7200
      TabIndex        =   45
      Top             =   5880
      Width           =   1935
   End
   Begin VB.TextBox txtPenerimaanBersih 
      Height          =   315
      Left            =   7200
      TabIndex        =   44
      Top             =   5520
      Width           =   1935
   End
   Begin VB.TextBox txtPencairanGiro 
      Height          =   315
      Left            =   7200
      TabIndex        =   43
      Top             =   5160
      Width           =   1935
   End
   Begin VB.TextBox txtCNPenjualan 
      Height          =   315
      Left            =   7200
      TabIndex        =   42
      Top             =   4800
      Width           =   1935
   End
   Begin VB.TextBox txtPenerimaanTunai 
      Height          =   315
      Left            =   7200
      TabIndex        =   41
      Top             =   4440
      Width           =   1935
   End
   Begin VB.TextBox txtProsenPenjualan 
      Height          =   315
      Left            =   7200
      TabIndex        =   40
      Top             =   4080
      Width           =   1935
   End
   Begin VB.TextBox txtTargetPenjualan 
      Height          =   315
      Left            =   7200
      TabIndex        =   39
      Top             =   3720
      Width           =   1935
   End
   Begin VB.TextBox txtPenjualanBersih 
      Height          =   315
      Left            =   7200
      TabIndex        =   38
      Top             =   3360
      Width           =   1935
   End
   Begin VB.TextBox txtReturPenjualan 
      Height          =   315
      Left            =   7200
      TabIndex        =   37
      Top             =   3000
      Width           =   1935
   End
   Begin VB.TextBox txtPenjualan 
      Height          =   315
      Left            =   7200
      TabIndex        =   36
      Top             =   2640
      Width           =   1935
   End
   Begin VB.TextBox txtJumlahFaktur 
      Height          =   315
      Left            =   7200
      TabIndex        =   35
      Top             =   2280
      Width           =   1935
   End
   Begin VB.TextBox txtNamaSales 
      Height          =   315
      Left            =   7200
      TabIndex        =   34
      Top             =   1920
      Width           =   1935
   End
   Begin VB.TextBox txtKodeSales 
      Height          =   315
      Left            =   7200
      TabIndex        =   33
      Top             =   1560
      Width           =   1935
   End
   Begin VB.TextBox txtBulan 
      Height          =   315
      Left            =   7200
      TabIndex        =   32
      Top             =   1200
      Width           =   1935
   End
   Begin VB.TextBox txtTahun 
      Height          =   315
      Left            =   7200
      TabIndex        =   31
      Top             =   840
      Width           =   1935
   End
   Begin VB.Label lblProsenPenerimaan 
      Caption         =   "Prosen Penerimaan:"
      Height          =   255
      Left            =   240
      TabIndex        =   30
      Top             =   6240
      Width           =   1935
   End
   Begin VB.Label lblTargetPenerimaan 
      Caption         =   "Target Penerimaan:"
      Height          =   255
      Left            =   240
      TabIndex        =   29
      Top             =   5880
      Width           =   1935
   End
   Begin VB.Label lblPenerimaanBersih 
      Caption         =   "Penerimaan Bersih:"
      Height          =   255
      Left            =   240
      TabIndex        =   28
      Top             =   5520
      Width           =   1935
   End
   Begin VB.Label lblPencairanGiro 
      Caption         =   "Pencairan Giro:"
      Height          =   255
      Left            =   240
      TabIndex        =   27
      Top             =   5160
      Width           =   1935
   End
   Begin VB.Label lblCNPenjualan 
      Caption         =   "CN Penjualan:"
      Height          =   255
      Left            =   240
      TabIndex        =   26
      Top             =   4800
      Width           =   1935
   End
   Begin VB.Label lblPenerimaanTunai 
      Caption         =   "Penerimaan Tunai:"
      Height          =   255
      Left            =   240
      TabIndex        =   25
      Top             =   4440
      Width           =   1935
   End
   Begin VB.Label lblProsenPenjualan 
      Caption         =   "Prosen Penjualan:"
      Height          =   255
      Left            =   240
      TabIndex        =   24
      Top             =   4080
      Width           =   1935
   End
   Begin VB.Label lblTargetPenjualan 
      Caption         =   "Target Penjualan:"
      Height          =   255
      Left            =   240
      TabIndex        =   23
      Top             =   3720
      Width           =   1935
   End
   Begin VB.Label lblPenjualanBersih 
      Caption         =   "Penjualan Bersih:"
      Height          =   255
      Left            =   240
      TabIndex        =   22
      Top             =   3360
      Width           =   1935
   End
   Begin VB.Label lblReturPenjualan 
      Caption         =   "Retur Penjualan:"
      Height          =   255
      Left            =   240
      TabIndex        =   21
      Top             =   3000
      Width           =   1935
   End
   Begin VB.Label lblPenjualan 
      Caption         =   "Penjualan:"
      Height          =   255
      Left            =   240
      TabIndex        =   20
      Top             =   2640
      Width           =   1935
   End
   Begin VB.Label lblJumlahFaktur 
      Caption         =   "Jumlah Faktur:"
      Height          =   255
      Left            =   240
      TabIndex        =   19
      Top             =   2280
      Width           =   1935
   End
   Begin VB.Label lblNamaSales 
      Caption         =   "Nama Sales:"
      Height          =   255
      Left            =   240
      TabIndex        =   18
      Top             =   1920
      Width           =   1935
   End
   Begin VB.Label lblKodeSales 
      Caption         =   "Kode Sales:"
      Height          =   255
      Left            =   240
      TabIndex        =   17
      Top             =   1560
      Width           =   1935
   End
   Begin VB.Label lblBulan 
      Caption         =   "Bulan (01-12):"
      Height          =   255
      Left            =   240
      TabIndex        =   16
      Top             =   1200
      Width           =   1935
   End
   Begin VB.Label lblTahun 
      Caption         =   "Tahun:"
      Height          =   255
      Left            =   240
      TabIndex        =   15
      Top             =   840
      Width           =   1935
   End
   Begin VB.Label lblInfo 
      Caption         =   "Info:"
      Height          =   255
      Left            =   240
      TabIndex        =   14
      Top             =   240
      Width           =   13215
   End
   Begin VB.Label lblResponse 
      Caption         =   "API Response:"
      Height          =   255
      Left            =   240
      TabIndex        =   13
      Top             =   6960
      Width           =   1935
   End
End
Attribute VB_Name = "frmOmset"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub cmdFind_Click()
    Dim tahun As String
    Dim bulan As String
    Dim kodesales As String
    Dim response As String
    Dim success As Boolean
    Dim data As String
    
    ' Validasi input
    tahun = Trim(txtTahun.Text)
    bulan = Trim(txtBulan.Text)
    kodesales = Trim(txtKodeSales.Text)
    
    If tahun = "" Or bulan = "" Or kodesales = "" Then
        lblInfo.Caption = "Error: Tahun, Bulan, dan Kode Sales harus diisi untuk Find"
        txtResponse.Text = "Error: Tahun, Bulan, dan Kode Sales harus diisi"
        Exit Sub
    End If
    
    ' Panggil API
    lblInfo.Caption = "Mencari data omset..."
    txtResponse.Text = "Loading..."
    DoEvents
    
    response = FindOmset(tahun, bulan, kodesales)
    txtResponse.Text = response
    
    ' Parse response
    success = IsAPISuccess(response)
    
    If success Then
        ' Parse data dari response
        data = ParseJSONValue(response, "data")
        
        If data <> "" Then
            ' Parse setiap field dari data
            txtTahun.Text = ParseJSONValue(data, "tahun")
            txtBulan.Text = ParseJSONValue(data, "bulan")
            txtKodeSales.Text = ParseJSONValue(data, "kodesales")
            txtNamaSales.Text = ParseJSONValue(data, "namasales")
            txtJumlahFaktur.Text = ParseJSONValue(data, "jumlahfaktur")
            txtPenjualan.Text = ParseJSONValue(data, "penjualan")
            txtReturPenjualan.Text = ParseJSONValue(data, "returpenjualan")
            txtPenjualanBersih.Text = ParseJSONValue(data, "penjualanbersih")
            txtTargetPenjualan.Text = ParseJSONValue(data, "targetpenjualan")
            txtProsenPenjualan.Text = ParseJSONValue(data, "prosenpenjualan")
            txtPenerimaanTunai.Text = ParseJSONValue(data, "penerimaantunai")
            txtCNPenjualan.Text = ParseJSONValue(data, "cnpenjualan")
            txtPencairanGiro.Text = ParseJSONValue(data, "pencairangiro")
            txtPenerimaanBersih.Text = ParseJSONValue(data, "penerimaanbersih")
            txtTargetPenerimaan.Text = ParseJSONValue(data, "targetpenerimaan")
            txtProsenPenerimaan.Text = ParseJSONValue(data, "prosenpenerimaan")
            
            lblInfo.Caption = "Data omset berhasil ditemukan"
        Else
            lblInfo.Caption = "Data tidak ditemukan"
        End If
    Else
        Dim errorMsg As String
        errorMsg = GetAPIErrorMessage(response)
        lblInfo.Caption = "Error: " & errorMsg
    End If
End Sub

Private Sub cmdAdd_Click()
    Dim tahun As String
    Dim bulan As String
    Dim kodesales As String
    Dim namasales As String
    Dim jsonPayload As String
    Dim response As String
    Dim success As Boolean
    
    ' Validasi input wajib
    tahun = Trim(txtTahun.Text)
    bulan = Trim(txtBulan.Text)
    kodesales = Trim(txtKodeSales.Text)
    
    If tahun = "" Or bulan = "" Or kodesales = "" Then
        lblInfo.Caption = "Error: Tahun, Bulan, dan Kode Sales harus diisi"
        txtResponse.Text = "Error: Tahun, Bulan, dan Kode Sales harus diisi"
        Exit Sub
    End If
    
    ' Ambil data dari form
    namasales = Trim(txtNamaSales.Text)
    
    ' Build JSON payload
    jsonPayload = BuildOmsetJSON( _
        tahun, _
        bulan, _
        kodesales, _
        namasales, _
        Val(txtJumlahFaktur.Text), _
        Val(txtPenjualan.Text), _
        Val(txtReturPenjualan.Text), _
        Val(txtPenjualanBersih.Text), _
        Val(txtTargetPenjualan.Text), _
        Val(txtProsenPenjualan.Text), _
        Val(txtPenerimaanTunai.Text), _
        Val(txtCNPenjualan.Text), _
        Val(txtPencairanGiro.Text), _
        Val(txtPenerimaanBersih.Text), _
        Val(txtTargetPenerimaan.Text), _
        Val(txtProsenPenerimaan.Text) _
    )
    
    ' Panggil API
    lblInfo.Caption = "Menambahkan data omset..."
    txtResponse.Text = "Loading..."
    DoEvents
    
    response = AddOmset(jsonPayload)
    txtResponse.Text = response
    
    ' Parse response
    success = IsAPISuccess(response)
    
    If success Then
        lblInfo.Caption = "Data omset berhasil ditambahkan"
    Else
        Dim errorMsg As String
        errorMsg = GetAPIErrorMessage(response)
        lblInfo.Caption = "Error: " & errorMsg
    End If
End Sub

Private Sub cmdDelete_Click()
    Dim tahun As String
    Dim bulan As String
    Dim response As String
    Dim success As Boolean
    
    ' Validasi input
    tahun = Trim(txtTahun.Text)
    bulan = Trim(txtBulan.Text)
    
    If tahun = "" Or bulan = "" Then
        lblInfo.Caption = "Error: Tahun dan Bulan harus diisi untuk Delete"
        txtResponse.Text = "Error: Tahun dan Bulan harus diisi"
        Exit Sub
    End If
    
    ' Konfirmasi
    Dim result As VbMsgBoxResult
    result = MsgBox("Apakah Anda yakin ingin menghapus semua data omset untuk tahun " & tahun & " bulan " & bulan & "?", vbYesNo + vbQuestion, "Konfirmasi Hapus")
    
    If result <> vbYes Then
        Exit Sub
    End If
    
    ' Panggil API
    lblInfo.Caption = "Menghapus data omset..."
    txtResponse.Text = "Loading..."
    DoEvents
    
    response = DeleteOmset(tahun, bulan)
    txtResponse.Text = response
    
    ' Parse response
    success = IsAPISuccess(response)
    
    If success Then
        lblInfo.Caption = "Data omset berhasil dihapus"
        ' Clear form
        ClearForm
    Else
        Dim errorMsg As String
        errorMsg = GetAPIErrorMessage(response)
        lblInfo.Caption = "Error: " & errorMsg
    End If
End Sub

Private Sub ClearForm()
    txtTahun.Text = ""
    txtBulan.Text = ""
    txtKodeSales.Text = ""
    txtNamaSales.Text = ""
    txtJumlahFaktur.Text = "0"
    txtPenjualan.Text = "0"
    txtReturPenjualan.Text = "0"
    txtPenjualanBersih.Text = "0"
    txtTargetPenjualan.Text = "0"
    txtProsenPenjualan.Text = "0"
    txtPenerimaanTunai.Text = "0"
    txtCNPenjualan.Text = "0"
    txtPencairanGiro.Text = "0"
    txtPenerimaanBersih.Text = "0"
    txtTargetPenerimaan.Text = "0"
    txtProsenPenerimaan.Text = "0"
    txtResponse.Text = ""
End Sub

Private Sub Form_Load()
    lblInfo.Caption = "Form Omset Management - Gunakan Find untuk mencari data, Add untuk menambah, Delete untuk menghapus berdasarkan tahun dan bulan"
    ClearForm
End Sub

