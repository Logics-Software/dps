VERSION 5.00
Begin VB.Form frmMasterBarang 
   Caption         =   "Master Barang Management"
   ClientHeight    =   7440
   ClientLeft      =   4950
   ClientTop       =   2475
   ClientWidth     =   9720
   LinkTopic       =   "Form1"
   ScaleHeight     =   7440
   ScaleWidth      =   9720
   Begin VB.CommandButton cmdDelete 
      Caption         =   "Delete"
      Height          =   375
      Left            =   7920
      TabIndex        =   21
      Top             =   5280
      Width           =   1215
   End
   Begin VB.CommandButton cmdUpdate 
      Caption         =   "Update"
      Height          =   375
      Left            =   6600
      TabIndex        =   20
      Top             =   5280
      Width           =   1215
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "Save"
      Height          =   375
      Left            =   5280
      TabIndex        =   19
      Top             =   5280
      Width           =   1215
   End
   Begin VB.CommandButton cmdLoad 
      Caption         =   "Load Data"
      Height          =   375
      Left            =   3960
      TabIndex        =   18
      Top             =   5280
      Width           =   1215
   End
   Begin VB.ComboBox cmbPrekursor 
      Height          =   315
      Left            =   2760
      Style           =   2  'Dropdown List
      TabIndex        =   14
      Top             =   4440
      Width           =   2175
   End
   Begin VB.ComboBox cmbOtt 
      Height          =   315
      Left            =   2760
      Style           =   2  'Dropdown List
      TabIndex        =   16
      Top             =   4080
      Width           =   2175
   End
   Begin VB.TextBox txtStokAkhir 
      Height          =   315
      Left            =   2760
      TabIndex        =   15
      Top             =   3720
      Width           =   2175
   End
   Begin VB.TextBox txtDiscountJual 
      Height          =   315
      Left            =   2760
      TabIndex        =   14
      Top             =   3360
      Width           =   2175
   End
   Begin VB.TextBox txtHargaJual 
      Height          =   315
      Left            =   2760
      TabIndex        =   13
      Top             =   3000
      Width           =   2175
   End
   Begin VB.TextBox txtDiscountBeli 
      Height          =   315
      Left            =   2760
      TabIndex        =   12
      Top             =   2640
      Width           =   2175
   End
   Begin VB.TextBox txtHargaBeli 
      Height          =   315
      Left            =   2760
      TabIndex        =   11
      Top             =   2280
      Width           =   2175
   End
   Begin VB.TextBox txtHPP 
      Height          =   315
      Left            =   2760
      TabIndex        =   10
      Top             =   1920
      Width           =   2175
   End
   Begin VB.TextBox txtNIE 
      Height          =   315
      Left            =   2760
      TabIndex        =   9
      Top             =   1560
      Width           =   2175
   End
   Begin VB.TextBox txtKandungan 
      Height          =   315
      Left            =   2760
      TabIndex        =   8
      Top             =   1200
      Width           =   2175
   End
   Begin VB.TextBox txtKodeSupplier 
      Height          =   315
      Left            =   2760
      TabIndex        =   7
      Top             =   840
      Width           =   2175
   End
   Begin VB.TextBox txtKodeGolongan 
      Height          =   315
      Left            =   2760
      TabIndex        =   6
      Top             =   480
      Width           =   2175
   End
   Begin VB.TextBox txtKodePabrik 
      Height          =   315
      Left            =   2760
      TabIndex        =   5
      Top             =   120
      Width           =   2175
   End
   Begin VB.TextBox txtSatuan 
      Height          =   315
      Left            =   720
      TabIndex        =   4
      Top             =   1200
      Width           =   1695
   End
   Begin VB.ComboBox cmbStatus 
      Height          =   315
      Left            =   720
      Style           =   2  'Dropdown List
      TabIndex        =   5
      Top             =   1680
      Width           =   1695
   End
   Begin VB.TextBox txtNamaBarang 
      Height          =   315
      Left            =   720
      TabIndex        =   3
      Top             =   840
      Width           =   1695
   End
   Begin VB.TextBox txtKodeBarang 
      Height          =   315
      Left            =   720
      TabIndex        =   2
      Top             =   480
      Width           =   1695
   End
   Begin VB.TextBox txtID 
      Height          =   315
      Left            =   720
      TabIndex        =   1
      Top             =   120
      Width           =   1215
   End
   Begin VB.ListBox lstData 
      Height          =   3615
      Left            =   5280
      TabIndex        =   0
      Top             =   120
      Width           =   4215
   End
   Begin VB.Label lblInfo 
      Caption         =   "Info:"
      Height          =   255
      Left            =   720
      TabIndex        =   22
      Top             =   5880
      Width           =   8775
   End
   Begin VB.Label Label1 
      Caption         =   "ID:"
      Height          =   255
      Left            =   720
      TabIndex        =   23
      Top             =   0
      Width           =   1695
   End
   Begin VB.Label Label2 
      Caption         =   "Kode Barang:"
      Height          =   255
      Left            =   720
      TabIndex        =   24
      Top             =   360
      Width           =   1695
   End
   Begin VB.Label Label3 
      Caption         =   "Nama Barang:"
      Height          =   255
      Left            =   720
      TabIndex        =   25
      Top             =   720
      Width           =   1695
   End
   Begin VB.Label lblStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   720
      TabIndex        =   26
      Top             =   1440
      Width           =   1695
   End
   Begin VB.Label Label4 
      Caption         =   "Satuan:"
      Height          =   255
      Left            =   720
      TabIndex        =   27
      Top             =   1080
      Width           =   1695
   End
   Begin VB.Label Label5 
      Caption         =   "Kode Pabrik:"
      Height          =   255
      Left            =   2760
      TabIndex        =   27
      Top             =   0
      Width           =   2175
   End
   Begin VB.Label Label6 
      Caption         =   "Kode Golongan:"
      Height          =   255
      Left            =   2760
      TabIndex        =   28
      Top             =   360
      Width           =   2175
   End
   Begin VB.Label Label7 
      Caption         =   "Kode Supplier:"
      Height          =   255
      Left            =   2760
      TabIndex        =   29
      Top             =   720
      Width           =   2175
   End
   Begin VB.Label Label8 
      Caption         =   "Kandungan:"
      Height          =   255
      Left            =   2760
      TabIndex        =   30
      Top             =   1080
      Width           =   2175
   End
   Begin VB.Label Label9 
      Caption         =   "NIE:"
      Height          =   255
      Left            =   2760
      TabIndex        =   31
      Top             =   1440
      Width           =   2175
   End
   Begin VB.Label Label10 
      Caption         =   "HPP:"
      Height          =   255
      Left            =   2760
      TabIndex        =   32
      Top             =   1800
      Width           =   2175
   End
   Begin VB.Label Label11 
      Caption         =   "Harga Beli:"
      Height          =   255
      Left            =   2760
      TabIndex        =   33
      Top             =   2160
      Width           =   2175
   End
   Begin VB.Label Label12 
      Caption         =   "Diskon Beli (%):"
      Height          =   255
      Left            =   2760
      TabIndex        =   34
      Top             =   2520
      Width           =   2175
   End
   Begin VB.Label Label13 
      Caption         =   "Harga Jual:"
      Height          =   255
      Left            =   2760
      TabIndex        =   35
      Top             =   2880
      Width           =   2175
   End
   Begin VB.Label Label14 
      Caption         =   "Diskon Jual (%):"
      Height          =   255
      Left            =   2760
      TabIndex        =   36
      Top             =   3240
      Width           =   2175
   End
   Begin VB.Label Label15 
      Caption         =   "Stok Akhir:"
      Height          =   255
      Left            =   2760
      TabIndex        =   37
      Top             =   3600
      Width           =   2175
   End
   Begin VB.Label Label16 
      Caption         =   "OOT:"
      Height          =   255
      Left            =   2760
      TabIndex        =   38
      Top             =   3960
      Width           =   2175
   End
   Begin VB.Label Label17 
      Caption         =   "Prekursor:"
      Height          =   255
      Left            =   2760
      TabIndex        =   39
      Top             =   4320
      Width           =   2175
   End
End
Attribute VB_Name = "frmMasterBarang"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

Private Sub Form_Load()
    cmbOtt.AddItem "tidak"
    cmbOtt.AddItem "ya"
    cmbOtt.ListIndex = 0
    
    cmbPrekursor.AddItem "tidak"
    cmbPrekursor.AddItem "ya"
    cmbPrekursor.ListIndex = 0

    cmbStatus.AddItem "aktif"
    cmbStatus.AddItem "nonaktif"
    cmbStatus.ListIndex = 0
 
    Call cmdLoad_Click
End Sub

Private Sub cmdLoad_Click()
    Dim response As String
    Dim success As String
    Dim data As String
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    Dim kode As String
    Dim nama As String
    
    On Error GoTo ErrorHandler
    
    lblInfo.Caption = "Loading data..."
    lstData.Clear
    
    response = GetAllMasterbarang(1, 100)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    success = ParseJSONValue(response, "success")
    If LCase(success) = "true" Then
        data = ParseJSONValue(response, "data")
        If Left(data, 1) = "[" Then
            startPos = InStr(data, "{")
            Do While startPos > 0
                endPos = InStr(startPos, data, "}")
                If endPos = 0 Then Exit Do
                item = Mid(data, startPos, endPos - startPos + 1)
                
                kode = ParseJSONValue(item, "kodebarang")
                nama = ParseJSONValue(item, "namabarang")
                
                If kode <> "" Then
                    lstData.AddItem kode & " - " & nama
                End If
                
                startPos = InStr(endPos, data, "{")
            Loop
        End If
        lblInfo.Caption = "Data loaded successfully"
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub cmdSave_Click()
    Dim response As String
    Dim kodebarang As String
    Dim namabarang As String
    
    On Error GoTo ErrorHandler
    
    kodebarang = Trim(txtKodeBarang.Text)
    namabarang = Trim(txtNamaBarang.Text)
    
    If kodebarang = "" Or namabarang = "" Then
        MsgBox "Kode Barang dan Nama Barang harus diisi!", vbExclamation
        Exit Sub
    End If
    
    lblInfo.Caption = "Saving data..."
    response = CreateMasterbarang( _
        kodebarang, _
        namabarang, _
        Trim(txtSatuan.Text), _
        Trim(txtKodePabrik.Text), _
        Trim(txtKodeGolongan.Text), _
        Trim(txtKodeSupplier.Text), _
        Trim(txtKandungan.Text), _
        cmbOtt.Text, _
        cmbPrekursor.Text, _
        Trim(txtNIE.Text), _
        Trim(txtHPP.Text), _
        Trim(txtHargaBeli.Text), _
        Trim(txtDiscountBeli.Text), _
        Trim(txtHargaJual.Text), _
        Trim(txtDiscountJual.Text), _
        Trim(txtStokAkhir.Text), _
        cmbStatus.Text)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        lblInfo.Caption = "Data berhasil disimpan"
        MsgBox "Data berhasil disimpan!", vbInformation
        Call ClearForm
        Call cmdLoad_Click
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub cmdUpdate_Click()
    Dim response As String
    Dim id As Long
    
    On Error GoTo ErrorHandler
    
    If Trim(txtID.Text) = "" Then
        MsgBox "ID harus diisi untuk update!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.Text)
    
    lblInfo.Caption = "Updating data..."
    response = UpdateMasterbarang( _
        id, _
        "", _
        Trim(txtNamaBarang.Text), _
        Trim(txtSatuan.Text), _
        Trim(txtKodePabrik.Text), _
        Trim(txtKodeGolongan.Text), _
        Trim(txtKodeSupplier.Text), _
        Trim(txtKandungan.Text), _
        cmbOtt.Text, _
        cmbPrekursor.Text, _
        Trim(txtNIE.Text), _
        Trim(txtHPP.Text), _
        Trim(txtHargaBeli.Text), _
        Trim(txtDiscountBeli.Text), _
        Trim(txtHargaJual.Text), _
        Trim(txtDiscountJual.Text), _
        Trim(txtStokAkhir.Text), _
        cmbStatus.Text)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        lblInfo.Caption = "Data berhasil diupdate"
        MsgBox "Data berhasil diupdate!", vbInformation
        Call cmdLoad_Click
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub cmdDelete_Click()
    Dim response As String
    Dim id As Long
    Dim confirmResult As VbMsgBoxResult
    
    On Error GoTo ErrorHandler
    
    If Trim(txtID.Text) = "" Then
        MsgBox "ID harus diisi untuk delete!", vbExclamation
        Exit Sub
    End If
    
    id = CLng(txtID.Text)
    confirmResult = MsgBox("Yakin ingin menghapus data dengan ID " & id & "?", vbYesNo + vbQuestion, "Konfirmasi")
    If confirmResult = vbNo Then Exit Sub
    
    lblInfo.Caption = "Deleting data..."
    response = DeleteMasterbarangById(id)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        lblInfo.Caption = "Data berhasil dihapus"
        MsgBox "Data berhasil dihapus!", vbInformation
        Call ClearForm
        Call cmdLoad_Click
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub lstData_Click()
    Dim selectedText As String
    Dim kodebarang As String
    Dim response As String
    Dim data As String
    
    On Error GoTo ErrorHandler
    
    If lstData.ListIndex < 0 Then Exit Sub
    
    selectedText = lstData.List(lstData.ListIndex)
    If InStr(selectedText, " - ") = 0 Then Exit Sub
    kodebarang = Trim(Left(selectedText, InStr(selectedText, " - ") - 1))
    
    response = GetMasterbarangByKode(kodebarang)
    
    If Left(response, 5) = "ERROR" Then
        lblInfo.Caption = "Error: " & response
        Exit Sub
    End If
    
    If IsAPISuccess(response) Then
        data = ParseJSONValue(response, "data")
        
        txtID.Text = ParseJSONValue(data, "id")
        txtKodeBarang.Text = ParseJSONValue(data, "kodebarang")
        txtNamaBarang.Text = ParseJSONValue(data, "namabarang")
        txtSatuan.Text = ParseJSONValue(data, "satuan")
        txtKodePabrik.Text = ParseJSONValue(data, "kodepabrik")
        txtKodeGolongan.Text = ParseJSONValue(data, "kodegolongan")
        txtKodeSupplier.Text = ParseJSONValue(data, "kodesupplier")
        txtKandungan.Text = ParseJSONValue(data, "kandungan")
        txtNIE.Text = ParseJSONValue(data, "nie")
        txtHPP.Text = ParseJSONValue(data, "hpp")
        txtHargaBeli.Text = ParseJSONValue(data, "hargabeli")
        txtDiscountBeli.Text = ParseJSONValue(data, "discountbeli")
        txtHargaJual.Text = ParseJSONValue(data, "hargajual")
        txtDiscountJual.Text = ParseJSONValue(data, "discountjual")
        txtStokAkhir.Text = ParseJSONValue(data, "stokakhir")
        
        Dim ootValue As String
        Dim prekursorValue As String
        ootValue = ParseJSONValue(data, "oot")
        prekursorValue = ParseJSONValue(data, "prekursor")
        
        If ootValue = "ya" Then
            cmbOtt.ListIndex = 1
        Else
            cmbOtt.ListIndex = 0
        End If
        
        If prekursorValue = "ya" Then
            cmbPrekursor.ListIndex = 1
        Else
            cmbPrekursor.ListIndex = 0
        End If

        Dim statusValue As String
        statusValue = LCase(ParseJSONValue(data, "status"))
        If statusValue = "nonaktif" Then
            cmbStatus.ListIndex = 1
        Else
            cmbStatus.ListIndex = 0
        End If
        
        lblInfo.Caption = "Data loaded: " & kodebarang
    Else
        lblInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If
    
    Exit Sub
ErrorHandler:
    lblInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub ClearForm()
    txtID.Text = ""
    txtKodeBarang.Text = ""
    txtNamaBarang.Text = ""
    txtSatuan.Text = ""
    txtKodePabrik.Text = ""
    txtKodeGolongan.Text = ""
    txtKodeSupplier.Text = ""
    txtKandungan.Text = ""
    txtNIE.Text = ""
    txtHPP.Text = ""
    txtHargaBeli.Text = ""
    txtDiscountBeli.Text = ""
    txtHargaJual.Text = ""
    txtDiscountJual.Text = ""
    txtStokAkhir.Text = ""
    cmbOtt.ListIndex = 0
    cmbPrekursor.ListIndex = 0
    cmbStatus.ListIndex = 0
End Sub


