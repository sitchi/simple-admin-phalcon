<html>
<head></head>
<body style="background-color: #E4E4E4;padding: 20px; margin: 0; min-width: 640px;">
<table border="0" cellspacing="0" width="530" style="color:#262626;background-color:#fff;margin:auto;border:1px solid #e1e1e1">
    <tbody>
    <!-- header -->
    <tr style="background:#305cce">
        <td style="padding-left:10px">
            <a target="_blank" style="text-decoration:none;color:inherit;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:normal;">
                <h1 style="color:#fff">{{ publicUrl }}</h1>
            </a>
        </td>
    </tr>
    </tbody>

    {{ content() }}

    <!--footer-->
    <tbody>
    <tr>
        <td align="right">
            <table border="0" cellspacing="0" cellpadding="0" style="padding-bottom:9px;" align="right">
                <tbody>
                <tr style="border-bottom:1px solid #999999;">
                    <td width="24" style="padding:0 7px 0 0;">
                        <a href="https://{{ publicUrl }}" style="border-width:0;">{{ publicUrl }}
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
