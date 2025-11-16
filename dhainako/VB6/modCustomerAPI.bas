Attribute VB_Name = "modCustomerAPI"
'---------------------------------------------------------------
' Module untuk API Mastercustomer
'---------------------------------------------------------------

Option Explicit

' Base URL API
Public Const API_BASE_URL As String = "http://localhost:8000/api/mastercustomer"

' ============================================
' MASTER CUSTOMER API WRAPPERS
' ============================================
Public Function GetAllMastercustomer(Optional page As Long = 1, Optional perPage As Long = 100, _
                                     Optional search As String = "", Optional status As String = "") As String
    Dim url As String
    Dim params As String
    
    params = "?page=" & page & "&per_page=" & perPage
    If search <> "" Then params = params & "&search=" & URLEncode(search)
    If status <> "" Then params = params & "&status=" & URLEncode(status)
    
    url = API_BASE_URL & params
    GetAllMastercustomer = CallAPI("GET", url)
End Function

Public Function GetMastercustomerById(id As Long) As String
    Dim url As String
    url = API_BASE_URL & "?id=" & id
    GetMastercustomerById = CallAPI("GET", url)
End Function

Public Function GetMastercustomerByKode(kodeCustomer As String) As String
    Dim url As String
    url = API_BASE_URL & "?kodecustomer=" & URLEncode(kodeCustomer)
    GetMastercustomerByKode = CallAPI("GET", url)
End Function

Public Function CreateMastercustomer(kodeCustomer As String, namaCustomer As String, _
                                     Optional namabadanusaha As String = "", _
                                     Optional alamatcustomer As String = "", _
                                     Optional kotacustomer As String = "", _
                                     Optional notelepon As String = "", _
                                     Optional kontakperson As String = "", _
                                     Optional statuspkp As String = "nonpkp", _
                                     Optional npwp As String = "", _
                                     Optional namawp As String = "", _
                                     Optional alamatwp As String = "", _
                                     Optional namaapoteker As String = "", _
                                     Optional nosipa As String = "", _
                                     Optional tanggaledsipa As String = "", _
                                     Optional noijinusaha As String = "", _
                                     Optional tanggaledijinusaha As String = "", _
                                     Optional nocdob As String = "", _
                                     Optional tanggaledcdob As String = "", _
                                     Optional latitude As String = "", _
                                     Optional longitude As String = "", _
                                     Optional userid As String = "", _
                                     Optional status As String = "baru") As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "kodecustomer=" & URLEncode(kodeCustomer) & _
               "&namacustomer=" & URLEncode(namaCustomer)
    
    If namabadanusaha <> "" Then postData = postData & "&namabadanusaha=" & URLEncode(namabadanusaha)
    If alamatcustomer <> "" Then postData = postData & "&alamatcustomer=" & URLEncode(alamatcustomer)
    If kotacustomer <> "" Then postData = postData & "&kotacustomer=" & URLEncode(kotacustomer)
    If notelepon <> "" Then postData = postData & "&notelepon=" & URLEncode(notelepon)
    If kontakperson <> "" Then postData = postData & "&kontakperson=" & URLEncode(kontakperson)
    postData = postData & "&statuspkp=" & URLEncode(statuspkp)
    If npwp <> "" Then postData = postData & "&npwp=" & URLEncode(npwp)
    If namawp <> "" Then postData = postData & "&namawp=" & URLEncode(namawp)
    If alamatwp <> "" Then postData = postData & "&alamatwp=" & URLEncode(alamatwp)
    If namaapoteker <> "" Then postData = postData & "&namaapoteker=" & URLEncode(namaapoteker)
    If nosipa <> "" Then postData = postData & "&nosipa=" & URLEncode(nosipa)
    If tanggaledsipa <> "" Then postData = postData & "&tanggaledsipa=" & URLEncode(tanggaledsipa)
    If noijinusaha <> "" Then postData = postData & "&noijinusaha=" & URLEncode(noijinusaha)
    If tanggaledijinusaha <> "" Then postData = postData & "&tanggaledijinusaha=" & URLEncode(tanggaledijinusaha)
    If nocdob <> "" Then postData = postData & "&nocdob=" & URLEncode(nocdob)
    If tanggaledcdob <> "" Then postData = postData & "&tanggaledcdob=" & URLEncode(tanggaledcdob)
    If latitude <> "" Then postData = postData & "&latitude=" & URLEncode(latitude)
    If longitude <> "" Then postData = postData & "&longitude=" & URLEncode(longitude)
    If userid <> "" Then postData = postData & "&userid=" & URLEncode(userid)
    postData = postData & "&status=" & URLEncode(status)
    
    CreateMastercustomer = CallAPI("POST", url, postData)
End Function

Public Function UpdateMastercustomer(id As Long, _
                                     Optional kodeCustomer As String = "", _
                                     Optional namaCustomer As String = "", _
                                     Optional namabadanusaha As String = "", _
                                     Optional alamatcustomer As String = "", _
                                     Optional kotacustomer As String = "", _
                                     Optional notelepon As String = "", _
                                     Optional kontakperson As String = "", _
                                     Optional statuspkp As String = "", _
                                     Optional npwp As String = "", _
                                     Optional namawp As String = "", _
                                     Optional alamatwp As String = "", _
                                     Optional namaapoteker As String = "", _
                                     Optional nosipa As String = "", _
                                     Optional tanggaledsipa As String = "", _
                                     Optional noijinusaha As String = "", _
                                     Optional tanggaledijinusaha As String = "", _
                                     Optional nocdob As String = "", _
                                     Optional tanggaledcdob As String = "", _
                                     Optional latitude As String = "", _
                                     Optional longitude As String = "", _
                                     Optional userid As String = "", _
                                     Optional status As String = "") As String
    Dim url As String
    Dim postData As String
    Dim hasData As Boolean
    
    url = API_BASE_URL
    postData = "_method=PUT&id=" & id
    hasData = False
    
    If kodeCustomer <> "" Then postData = postData & "&kodecustomer=" & URLEncode(kodeCustomer): hasData = True
    If namaCustomer <> "" Then postData = postData & "&namacustomer=" & URLEncode(namaCustomer): hasData = True
    If namabadanusaha <> "" Then postData = postData & "&namabadanusaha=" & URLEncode(namabadanusaha): hasData = True
    If alamatcustomer <> "" Then postData = postData & "&alamatcustomer=" & URLEncode(alamatcustomer): hasData = True
    If kotacustomer <> "" Then postData = postData & "&kotacustomer=" & URLEncode(kotacustomer): hasData = True
    If notelepon <> "" Then postData = postData & "&notelepon=" & URLEncode(notelepon): hasData = True
    If kontakperson <> "" Then postData = postData & "&kontakperson=" & URLEncode(kontakperson): hasData = True
    If statuspkp <> "" Then postData = postData & "&statuspkp=" & URLEncode(statuspkp): hasData = True
    If npwp <> "" Then postData = postData & "&npwp=" & URLEncode(npwp): hasData = True
    If namawp <> "" Then postData = postData & "&namawp=" & URLEncode(namawp): hasData = True
    If alamatwp <> "" Then postData = postData & "&alamatwp=" & URLEncode(alamatwp): hasData = True
    If namaapoteker <> "" Then postData = postData & "&namaapoteker=" & URLEncode(namaapoteker): hasData = True
    If nosipa <> "" Then postData = postData & "&nosipa=" & URLEncode(nosipa): hasData = True
    If tanggaledsipa <> "" Then postData = postData & "&tanggaledsipa=" & URLEncode(tanggaledsipa): hasData = True
    If noijinusaha <> "" Then postData = postData & "&noijinusaha=" & URLEncode(noijinusaha): hasData = True
    If tanggaledijinusaha <> "" Then postData = postData & "&tanggaledijinusaha=" & URLEncode(tanggaledijinusaha): hasData = True
    If nocdob <> "" Then postData = postData & "&nocdob=" & URLEncode(nocdob): hasData = True
    If tanggaledcdob <> "" Then postData = postData & "&tanggaledcdob=" & URLEncode(tanggaledcdob): hasData = True
    If latitude <> "" Then postData = postData & "&latitude=" & URLEncode(latitude): hasData = True
    If longitude <> "" Then postData = postData & "&longitude=" & URLEncode(longitude): hasData = True
    If userid <> "" Then postData = postData & "&userid=" & URLEncode(userid): hasData = True
    If status <> "" Then postData = postData & "&status=" & URLEncode(status): hasData = True
    
    If hasData Then
        UpdateMastercustomer = CallAPI("POST", url, postData)
    Else
        UpdateMastercustomer = "ERROR: No data to update"
    End If
End Function

Public Function UpdateMastercustomerStatusByKode(kodeCustomer As String, status As String) As String
    Dim url As String
    Dim postData As String

    url = API_BASE_URL
    postData = "action=update_status&kodecustomer=" & URLEncode(kodeCustomer) & _
               "&status=" & URLEncode(status)

    UpdateMastercustomerStatusByKode = CallAPI("POST", url, postData)
End Function

Public Function DeleteMastercustomerById(id As Long) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=DELETE&id=" & id
    
    DeleteMastercustomerById = CallAPI("POST", url, postData)
End Function

Public Function DeleteMastercustomerByKode(kodeCustomer As String) As String
    Dim url As String
    Dim postData As String
    
    url = API_BASE_URL
    postData = "_method=DELETE&kodecustomer=" & URLEncode(kodeCustomer)
    
    DeleteMastercustomerByKode = CallAPI("POST", url, postData)
End Function
