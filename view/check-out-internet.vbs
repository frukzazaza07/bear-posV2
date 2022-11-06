Dim oXMLHTTP
Dim oStream
Set oXMLHTTP = CreateObject("MSXML2.XMLHTTP.3.0")
oXMLHTTP.Open "GET", "https://www.google.com", True
oXMLHTTP.Send
Set objShell = WScript.CreateObject("WScript.Shell")
If oXMLHTTP.Status = 200 Then
    ' Set oStream = CreateObject("ADODB.Stream")
    ' oStream.Open
    ' oStream.Type = 1
    ' oStream.Write oXMLHTTP.responseBody
    ' oStream.Close
	' ส่ง ค่าไป server obm
	Set objExecObject = objShell.Exec("opcmon policy_name=200")
 Else
	Set objExecObject = objShell.Exec("opcmon policy_name=500")
End If


