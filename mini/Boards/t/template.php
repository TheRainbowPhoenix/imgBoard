<html>
<head>
  <pmg:if page="index">
    <title>A gallery of <pmg:count/> pictures</title>
  </pmg:if>
  <pmg:if page="picture">
    <title>Picture <pmg:current/>/<pmg:count/></title>
  </pmg:if>
  <style type="text/css"><!--
    body {
      background-color: #FFFFFF;
      font-family: arial,helvetica,sans-serif;
      font-size: 16px
    }

    a {
      text-decoration: none;
    }

    address {
      margin-top: 10px;
      border-top: 1px solid #000000;
      font-size: 80%;
      text-align: center;
    }

    .tabindex {
      width: 100%;
    }

    .tabindex TD {
      width: auto;
      background-color: #C0C0C0;
      text-align: center;
      height: 110px;
    }

    .thumbimg {
      background-color: #000000;
      padding: 3px;
    }

    .picture {
      background-color: #C0C0C0;
      text-align: center;
      padding: 5px;
    }

    .picimg {
      background-color: #000000;
      padding: 5px;
      margin-bottom: 3px;
    }
  //--></style>
</head>
<body>
  <pmg:if page="index">
    <div align="right">
      <pmg:first>[ Start... ]</pmg:first>
    </div>
    <pmg:index/>
  </pmg:if>

  <pmg:if page="picture">
    <div align="right">
      <pmg:toc>[ Index ]&nbsp;</pmg:toc>
      <pmg:first>[ First ]&nbsp;</pmg:first>
      <pmg:prev>[ Previous ]&nbsp;</pmg:prev>
      <pmg:next>[ Next ]&nbsp;</pmg:next>
      <pmg:last>[ Last ]</pmg:last>
    </div>
    <div class="picture">
      <pmg:image/><br />
      <pmg:caption/>
    </div>
  </pmg:if>
</body>
</html>
