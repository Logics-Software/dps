VERSION 5.00
Begin VB.Form frmMastercustomer 
   Caption         =   "Master Customer Management"
   ClientHeight    =   7800
   ClientLeft      =   120
   ClientTop       =   465
   ClientWidth     =   13260
   LinkTopic       =   "Form2"
   ScaleHeight     =   7800
   ScaleWidth      =   13260
   StartUpPosition =   3  'Windows Default
   Begin VB.CommandButton cmdSetKoordinat 
      Caption         =   "Set Koordinat"
      Height          =   375
      Left            =   10125
      TabIndex        =   50
      Top             =   4095
      Width           =   1335
   End
   Begin VB.CommandButton cmdCustomerDelete 
      Caption         =   "Delete"
      Height          =   375
      Left            =   8520
      TabIndex        =   27
      Top             =   4080
      Width           =   1335
   End
   Begin VB.CommandButton cmdCustomerUpdate 
      Caption         =   "Update"
      Height          =   375
      Left            =   7080
      TabIndex        =   26
      Top             =   4080
      Width           =   1335
   End
   Begin VB.CommandButton cmdCustomerSave 
      Caption         =   "Save"
      Height          =   375
      Left            =   5640
      TabIndex        =   25
      Top             =   4080
      Width           =   1335
   End
   Begin VB.CommandButton cmdCustomerLoad 
      Caption         =   "Load Data"
      Height          =   375
      Left            =   4200
      TabIndex        =   24
      Top             =   4080
      Width           =   1335
   End
   Begin VB.ComboBox cmbCustomerStatus 
      Height          =   315
      Left            =   1680
      Style           =   2  'Dropdown List
      TabIndex        =   23
      Top             =   3720
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerUserID 
      Height          =   315
      Left            =   1680
      TabIndex        =   22
      Top             =   3360
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerLongitude 
      Height          =   315
      Left            =   1680
      TabIndex        =   21
      Top             =   3000
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerLatitude 
      Height          =   315
      Left            =   1680
      TabIndex        =   20
      Top             =   2640
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerTanggalEdCDOB 
      Height          =   315
      Left            =   1680
      TabIndex        =   19
      Top             =   2280
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerNoCDOB 
      Height          =   315
      Left            =   1680
      TabIndex        =   18
      Top             =   1920
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerTanggalEdIjin 
      Height          =   315
      Left            =   1680
      TabIndex        =   17
      Top             =   1560
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerNoIjinUsaha 
      Height          =   315
      Left            =   1680
      TabIndex        =   16
      Top             =   1200
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerTanggalEdSIPA 
      Height          =   315
      Left            =   1680
      TabIndex        =   15
      Top             =   840
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerNoSIPA 
      Height          =   315
      Left            =   1680
      TabIndex        =   14
      Top             =   480
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerNamaApoteker 
      Height          =   315
      Left            =   1680
      TabIndex        =   13
      Top             =   120
      Width           =   1935
   End
   Begin VB.TextBox txtCustomerAlamatWP 
      Height          =   315
      Left            =   5280
      TabIndex        =   13
      Top             =   2760
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerNamaWP 
      Height          =   315
      Left            =   5280
      TabIndex        =   12
      Top             =   2400
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerNPWP 
      Height          =   315
      Left            =   5280
      TabIndex        =   11
      Top             =   2040
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerKontakPerson 
      Height          =   315
      Left            =   5280
      TabIndex        =   9
      Top             =   1320
      Width           =   2415
   End
   Begin VB.ComboBox cmbCustomerStatusPKP 
      Enabled         =   0   'False
      Height          =   315
      Left            =   5280
      Style           =   2  'Dropdown List
      TabIndex        =   10
      Top             =   1680
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerNoTelepon 
      Height          =   315
      Left            =   5280
      TabIndex        =   8
      Top             =   960
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerKota 
      Height          =   315
      Left            =   5280
      TabIndex        =   7
      Top             =   600
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerAlamat 
      Height          =   315
      Left            =   5280
      TabIndex        =   6
      Top             =   240
      Width           =   2415
   End
   Begin VB.TextBox txtCustomerNamaBadanUsaha 
      Height          =   315
      Left            =   9510
      TabIndex        =   5
      Top             =   2040
      Width           =   2055
   End
   Begin VB.TextBox txtCustomerNama 
      Height          =   315
      Left            =   9510
      TabIndex        =   4
      Top             =   1680
      Width           =   2055
   End
   Begin VB.TextBox txtCustomerKode 
      Height          =   315
      Left            =   9510
      TabIndex        =   3
      Top             =   1320
      Width           =   2055
   End
   Begin VB.TextBox txtCustomerID 
      Height          =   315
      Left            =   9510
      TabIndex        =   2
      Top             =   960
      Width           =   1215
   End
   Begin VB.ListBox lstCustomerData 
      Height          =   2400
      Left            =   4200
      TabIndex        =   1
      Top             =   4560
      Width           =   5655
   End
   Begin VB.Label lblCustomerStatus 
      Caption         =   "Status:"
      Height          =   255
      Left            =   240
      TabIndex        =   48
      Top             =   3720
      Width           =   1335
   End
   Begin VB.Label lblCustomerUserID 
      Caption         =   "User ID:"
      Height          =   255
      Left            =   240
      TabIndex        =   47
      Top             =   3360
      Width           =   1335
   End
   Begin VB.Label lblCustomerLongitude 
      Caption         =   "Longitude:"
      Height          =   255
      Left            =   240
      TabIndex        =   46
      Top             =   3000
      Width           =   1335
   End
   Begin VB.Label lblCustomerLatitude 
      Caption         =   "Latitude:"
      Height          =   255
      Left            =   240
      TabIndex        =   45
      Top             =   2640
      Width           =   1335
   End
   Begin VB.Label lblCustomerTanggalEdCDOB 
      Caption         =   "Tgl. ED CDOB:"
      Height          =   255
      Left            =   240
      TabIndex        =   44
      Top             =   2280
      Width           =   1335
   End
   Begin VB.Label lblCustomerNoCDOB 
      Caption         =   "No. CDOB:"
      Height          =   255
      Left            =   240
      TabIndex        =   43
      Top             =   1920
      Width           =   1335
   End
   Begin VB.Label lblCustomerTanggalEdIjin 
      Caption         =   "Tgl. ED Ijin Usaha:"
      Height          =   255
      Left            =   240
      TabIndex        =   42
      Top             =   1560
      Width           =   1335
   End
   Begin VB.Label lblCustomerNoIjinUsaha 
      Caption         =   "No. Ijin Usaha:"
      Height          =   255
      Left            =   240
      TabIndex        =   41
      Top             =   1200
      Width           =   1335
   End
   Begin VB.Label lblCustomerTanggalEdSIPA 
      Caption         =   "Tgl. ED SIPA:"
      Height          =   255
      Left            =   240
      TabIndex        =   40
      Top             =   840
      Width           =   1335
   End
   Begin VB.Label lblCustomerNoSIPA 
      Caption         =   "No. SIPA:"
      Height          =   255
      Left            =   240
      TabIndex        =   39
      Top             =   480
      Width           =   1335
   End
   Begin VB.Label lblCustomerNamaApoteker 
      Caption         =   "Nama Apoteker:"
      Height          =   255
      Left            =   240
      TabIndex        =   38
      Top             =   120
      Width           =   1335
   End
   Begin VB.Label lblCustomerAlamatWP 
      Caption         =   "Alamat WP:"
      Height          =   255
      Left            =   3720
      TabIndex        =   37
      Top             =   2760
      Width           =   1455
   End
   Begin VB.Label lblCustomerNamaWP 
      Caption         =   "Nama WP:"
      Height          =   255
      Left            =   3720
      TabIndex        =   36
      Top             =   2400
      Width           =   1455
   End
   Begin VB.Label lblCustomerNPWP 
      Caption         =   "NPWP:"
      Height          =   255
      Left            =   3720
      TabIndex        =   35
      Top             =   2040
      Width           =   1455
   End
   Begin VB.Label lblCustomerStatusPKP 
      Caption         =   "Status PKP:"
      Height          =   255
      Left            =   3720
      TabIndex        =   34
      Top             =   1680
      Width           =   1455
   End
   Begin VB.Label lblCustomerKontakPerson 
      Caption         =   "Kontak Person:"
      Height          =   255
      Left            =   3720
      TabIndex        =   33
      Top             =   1320
      Width           =   1455
   End
   Begin VB.Label lblCustomerNoTelepon 
      Caption         =   "No Telepon:"
      Height          =   255
      Left            =   3720
      TabIndex        =   33
      Top             =   960
      Width           =   1455
   End
   Begin VB.Label lblCustomerKota 
      Caption         =   "Kota:"
      Height          =   255
      Left            =   3720
      TabIndex        =   32
      Top             =   600
      Width           =   1455
   End
   Begin VB.Label lblCustomerAlamat 
      Caption         =   "Alamat Customer:"
      Height          =   255
      Left            =   3720
      TabIndex        =   31
      Top             =   240
      Width           =   1455
   End
   Begin VB.Label lblCustomerNamaBadanUsaha 
      Caption         =   "Nama Badan Usaha:"
      Height          =   255
      Left            =   7950
      TabIndex        =   30
      Top             =   2040
      Width           =   1455
   End
   Begin VB.Label lblCustomerNama 
      Caption         =   "Nama Customer:"
      Height          =   255
      Left            =   7950
      TabIndex        =   29
      Top             =   1680
      Width           =   1455
   End
   Begin VB.Label lblCustomerKode 
      Caption         =   "Kode Customer:"
      Height          =   255
      Left            =   7950
      TabIndex        =   28
      Top             =   1320
      Width           =   1455
   End
   Begin VB.Label lblCustomerID 
      Caption         =   "ID:"
      Height          =   255
      Left            =   7950
      TabIndex        =   0
      Top             =   960
      Width           =   1455
   End
   Begin VB.Label lblCustomerInfo 
      Caption         =   "Info:"
      Height          =   255
      Left            =   240
      TabIndex        =   49
      Top             =   4560
      Width           =   3735
   End
End
Attribute VB_Name = "frmMastercustomer"
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
 
    cmbCustomerStatus.AddItem "baru"
    cmbCustomerStatus.AddItem "updated"
    cmbCustomerStatus.AddItem "aktif"
    cmbCustomerStatus.AddItem "nonaktif"
    cmbCustomerStatus.ListIndex = 0
 
    cmbCustomerStatusPKP.Clear
    cmbCustomerStatusPKP.AddItem "pkp"
    cmbCustomerStatusPKP.AddItem "nonpkp"
    cmbCustomerStatusPKP.ListIndex = 0
    ApplyStatusPkpState "pkp"
 
    Call cmdCustomerLoad_Click
End Sub

Private Sub ApplyStatusPkpState(ByVal statusValue As String, Optional ByVal preserveValues As Boolean = False)
    Dim isPkp As Boolean
    isPkp = (LCase$(Trim$(statusValue)) = "pkp")

    If isPkp Then
        txtCustomerNPWP.Enabled = True
        txtCustomerNamaWP.Enabled = True
        txtCustomerAlamatWP.Enabled = True
    Else
        txtCustomerNPWP.Enabled = False
        txtCustomerNamaWP.Enabled = False
        txtCustomerAlamatWP.Enabled = False

        If Not preserveValues Then
            txtCustomerNPWP.Text = ""
            txtCustomerNamaWP.Text = ""
            txtCustomerAlamatWP.Text = ""
        End If
    End If
End Sub

Private Sub cmdCustomerLoad_Click()
    Dim response As String
    Dim success As String
    Dim data As String
    Dim startPos As Long
    Dim endPos As Long
    Dim item As String
    Dim kode As String
    Dim nama As String
    Dim statusPkp As String

    On Error GoTo ErrorHandler

    lblCustomerInfo.Caption = "Loading data..."
    lstCustomerData.Clear

    response = GetAllMastercustomer(1, 100)

    If Left(response, 5) = "ERROR" Then
        lblCustomerInfo.Caption = "Error: " & response
        Exit Sub
    End If

    success = ParseJSONValue(response, "success")

    If LCase(success) = "true" Then
        data = ParseJSONValue(response, "data")

        If Left(data, 1) = "[" Then
            startPos = 2
            Do While startPos < Len(data)
                endPos = InStr(startPos, data, "}")
                If endPos = 0 Then Exit Do

                item = Mid(data, startPos, endPos - startPos + 1)
                kode = ParseJSONValue(item, "kodecustomer")
                nama = ParseJSONValue(item, "namacustomer")

                If kode <> "" Then lstCustomerData.AddItem kode & " - " & nama

                startPos = InStr(endPos, data, "{")
                If startPos = 0 Then Exit Do
            Loop
        End If

        lblCustomerInfo.Caption = "Data loaded successfully"
    Else
        lblCustomerInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If

    Exit Sub

ErrorHandler:
    lblCustomerInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub cmdCustomerSave_Click()
    Dim response As String
    Dim kode As String
    Dim nama As String

    On Error GoTo ErrorHandler

    kode = Trim(txtCustomerKode.text)
    nama = Trim(txtCustomerNama.text)

    If kode = "" Or nama = "" Then
        MsgBox "Kode Customer dan Nama Customer harus diisi!", vbExclamation
        Exit Sub
    End If

    lblCustomerInfo.Caption = "Saving data..."

    statusPkp = LCase$(Trim$(cmbCustomerStatusPKP.Text))
    If statusPkp <> "pkp" Then
        txtCustomerNPWP.Text = ""
        txtCustomerNamaWP.Text = ""
        txtCustomerAlamatWP.Text = ""
    End If

    response = CreateMastercustomer( _
        kode, _
        nama, _
        Trim(txtCustomerNamaBadanUsaha.text), _
        Trim(txtCustomerAlamat.text), _
        Trim(txtCustomerKota.text), _
        Trim(txtCustomerNoTelepon.text), _
        Trim(txtCustomerKontakPerson.text), _
        statusPkp, _
        Trim(txtCustomerNPWP.text), _
        Trim(txtCustomerNamaWP.text), _
        Trim(txtCustomerAlamatWP.text), _
        Trim(txtCustomerNamaApoteker.text), _
        Trim(txtCustomerNoSIPA.text), _
        Trim(txtCustomerTanggalEdSIPA.text), _
        Trim(txtCustomerNoIjinUsaha.text), _
        Trim(txtCustomerTanggalEdIjin.text), _
        Trim(txtCustomerNoCDOB.text), _
        Trim(txtCustomerTanggalEdCDOB.text), _
        Trim(txtCustomerLatitude.text), _
        Trim(txtCustomerLongitude.text), _
        Trim(txtCustomerUserID.text), _
        cmbCustomerStatus.text)

    If Left(response, 5) = "ERROR" Then
        lblCustomerInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If

    If IsAPISuccess(response) Then
        lblCustomerInfo.Caption = "Data berhasil disimpan"
        MsgBox "Data berhasil disimpan!", vbInformation
        Call ClearCustomerForm
        Call cmdCustomerLoad_Click
    Else
        lblCustomerInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If

    Exit Sub

ErrorHandler:
    lblCustomerInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub cmdCustomerUpdate_Click()
    Dim response As String
    Dim id As Long
    Dim statusPkp As String

    On Error GoTo ErrorHandler

    If Trim(txtCustomerID.text) = "" Then
        MsgBox "ID harus diisi untuk update!", vbExclamation
        Exit Sub
    End If

    id = CLng(txtCustomerID.text)

    lblCustomerInfo.Caption = "Updating data..."

    statusPkp = LCase$(Trim$(cmbCustomerStatusPKP.Text))
    If statusPkp <> "pkp" Then
        txtCustomerNPWP.Text = ""
        txtCustomerNamaWP.Text = ""
        txtCustomerAlamatWP.Text = ""
    End If

    response = UpdateMastercustomer( _
        id, _
        Trim(txtCustomerKode.text), _
        Trim(txtCustomerNama.text), _
        Trim(txtCustomerNamaBadanUsaha.text), _
        Trim(txtCustomerAlamat.text), _
        Trim(txtCustomerKota.text), _
        Trim(txtCustomerNoTelepon.text), _
        Trim(txtCustomerKontakPerson.text), _
        statusPkp, _
        Trim(txtCustomerNPWP.text), _
        Trim(txtCustomerNamaWP.text), _
        Trim(txtCustomerAlamatWP.text), _
        Trim(txtCustomerNamaApoteker.text), _
        Trim(txtCustomerNoSIPA.text), _
        Trim(txtCustomerTanggalEdSIPA.text), _
        Trim(txtCustomerNoIjinUsaha.text), _
        Trim(txtCustomerTanggalEdIjin.text), _
        Trim(txtCustomerNoCDOB.text), _
        Trim(txtCustomerTanggalEdCDOB.text), _
        Trim(txtCustomerLatitude.text), _
        Trim(txtCustomerLongitude.text), _
        Trim(txtCustomerUserID.text), _
        cmbCustomerStatus.text)

    If Left(response, 5) = "ERROR" Then
        lblCustomerInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If

    If IsAPISuccess(response) Then
        lblCustomerInfo.Caption = "Data berhasil diupdate"
        MsgBox "Data berhasil diupdate!", vbInformation
        Call cmdCustomerLoad_Click
    Else
        lblCustomerInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If

    Exit Sub

ErrorHandler:
    lblCustomerInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub cmdCustomerDelete_Click()
    Dim response As String
    Dim id As Long
    Dim confirm As VbMsgBoxResult

    On Error GoTo ErrorHandler

    If Trim(txtCustomerID.text) = "" Then
        MsgBox "ID harus diisi untuk delete!", vbExclamation
        Exit Sub
    End If

    id = CLng(txtCustomerID.text)

    confirm = MsgBox("Yakin ingin menghapus data dengan ID " & id & "?", vbYesNo + vbQuestion, "Konfirmasi")
    If confirm = vbNo Then Exit Sub

    lblCustomerInfo.Caption = "Deleting data..."

    response = DeleteMastercustomerById(id)

    If Left(response, 5) = "ERROR" Then
        lblCustomerInfo.Caption = "Error: " & response
        MsgBox "Error: " & response, vbCritical
        Exit Sub
    End If

    If IsAPISuccess(response) Then
        lblCustomerInfo.Caption = "Data berhasil dihapus"
        MsgBox "Data berhasil dihapus!", vbInformation
        Call ClearCustomerForm
        Call cmdCustomerLoad_Click
    Else
        lblCustomerInfo.Caption = "Error: " & GetAPIErrorMessage(response)
        MsgBox "Error: " & GetAPIErrorMessage(response), vbCritical
    End If

    Exit Sub

ErrorHandler:
    lblCustomerInfo.Caption = "Error: " & Err.Description
    MsgBox "Error: " & Err.Description, vbCritical
End Sub

Private Sub lstCustomerData_Click()
    Dim selectedText As String
    Dim kode As String
    Dim response As String
    Dim data As String

    On Error GoTo ErrorHandler

    If lstCustomerData.ListIndex < 0 Then Exit Sub

    selectedText = lstCustomerData.List(lstCustomerData.ListIndex)
    If InStr(selectedText, " - ") = 0 Then Exit Sub

    kode = Trim(Left(selectedText, InStr(selectedText, " - ") - 1))
    If kode = "" Then Exit Sub

    response = GetMastercustomerByKode(kode)

    If Left(response, 5) = "ERROR" Then
        lblCustomerInfo.Caption = "Error: " & response
        Exit Sub
    End If

    If IsAPISuccess(response) Then
        data = ParseJSONValue(response, "data")

        txtCustomerID.text = ParseJSONValue(data, "id")
        txtCustomerKode.text = ParseJSONValue(data, "kodecustomer")
        txtCustomerNama.text = ParseJSONValue(data, "namacustomer")
        txtCustomerNamaBadanUsaha.text = ParseJSONValue(data, "namabadanusaha")
        txtCustomerAlamat.text = ParseJSONValue(data, "alamatcustomer")
        txtCustomerKota.text = ParseJSONValue(data, "kotacustomer")
        txtCustomerNoTelepon.text = ParseJSONValue(data, "notelepon")
        txtCustomerKontakPerson.text = ParseJSONValue(data, "kontakperson")
        txtCustomerNamaApoteker.text = ParseJSONValue(data, "namaapoteker")
        txtCustomerNoSIPA.text = ParseJSONValue(data, "nosipa")
        txtCustomerTanggalEdSIPA.text = ParseJSONValue(data, "tanggaledsipa")
        txtCustomerNoIjinUsaha.text = ParseJSONValue(data, "noijinusaha")
        txtCustomerTanggalEdIjin.text = ParseJSONValue(data, "tanggaledijinusaha")
        txtCustomerNoCDOB.text = ParseJSONValue(data, "nocdob")
        txtCustomerTanggalEdCDOB.text = ParseJSONValue(data, "tanggaledcdob")
        txtCustomerLatitude.text = ParseJSONValue(data, "latitude")
        txtCustomerLongitude.text = ParseJSONValue(data, "longitude")
        txtCustomerUserID.text = ParseJSONValue(data, "userid")

        Dim statusValue As String
        statusValue = ParseJSONValue(data, "status")
        Select Case LCase(statusValue)
            Case "baru"
                cmbCustomerStatus.ListIndex = 0
            Case "updated"
                cmbCustomerStatus.ListIndex = 1
            Case "aktif"
                cmbCustomerStatus.ListIndex = 2
            Case "nonaktif"
                cmbCustomerStatus.ListIndex = 3
            Case Else
                cmbCustomerStatus.ListIndex = 0
        End Select

        lblCustomerInfo.Caption = "Data loaded: " & kode

        Dim statusPkpValue As String
        statusPkpValue = LCase$(ParseJSONValue(data, "statuspkp"))
        If statusPkpValue = "pkp" Then
            cmbCustomerStatusPKP.ListIndex = 0
            ApplyStatusPkpState statusPkpValue, True
            txtCustomerNPWP.Text = ParseJSONValue(data, "npwp")
            txtCustomerNamaWP.Text = ParseJSONValue(data, "namawp")
            txtCustomerAlamatWP.Text = ParseJSONValue(data, "alamatwp")
        Else
            cmbCustomerStatusPKP.ListIndex = 1
            ApplyStatusPkpState statusPkpValue
        End If
    Else
        lblCustomerInfo.Caption = "Error: " & GetAPIErrorMessage(response)
    End If

    Exit Sub

ErrorHandler:
    lblCustomerInfo.Caption = "Error: " & Err.Description
End Sub

Private Sub ClearCustomerForm()
    txtCustomerID.text = ""
    txtCustomerKode.text = ""
    txtCustomerNama.text = ""
    txtCustomerNamaBadanUsaha.text = ""
    txtCustomerAlamat.text = ""
    txtCustomerKota.text = ""
    txtCustomerNoTelepon.text = ""
    txtCustomerKontakPerson.text = ""
    txtCustomerNPWP.text = ""
    txtCustomerNamaWP.text = ""
    txtCustomerAlamatWP.text = ""
    txtCustomerNamaApoteker.text = ""
    txtCustomerNoSIPA.text = ""
    txtCustomerTanggalEdSIPA.text = ""
    txtCustomerNoIjinUsaha.text = ""
    txtCustomerTanggalEdIjin.text = ""
    txtCustomerNoCDOB.text = ""
    txtCustomerTanggalEdCDOB.text = ""
    txtCustomerLatitude.text = ""
    txtCustomerLongitude.text = ""
    txtCustomerUserID.text = ""
    cmbCustomerStatus.ListIndex = 0
    cmbCustomerStatusPKP.ListIndex = 0
    ApplyStatusPkpState "pkp"
End Sub

Private Sub cmdSetKoordinat_Click()
    Shell """C:\Program Files\Google\Chrome\Application\chrome.exe"" --app=""http://localhost:8000/mastercustomer/map?customer_id=""" & Trim(txtCustomerID.text) & """, vbNormalFocus)"
End Sub
