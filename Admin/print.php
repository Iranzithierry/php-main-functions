<!DOCTYPE html>
<html>
<head>
  <title>Print Table Example</title>
  <style>
    /* Hide everything except the table when printing */
    @media print {
      body * {
        visibility: hidden;
      }
      table {
        visibility: visible;
      }
    }
  </style>
</head>
<body>
  <table id="myTable">
    <thead>
      <tr>
        <th>Column 1</th>
        <th>Column 2</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Row 1, Column 1</td>
        <td>Row 1, Column 2</td>
      </tr>
      <tr>
        <td>Row 2, Column 1</td>
        <td>Row 2, Column 2</td>
      </tr>
    </tbody>
  </table>

  <!-- Add a print button -->
  <button onclick="printTable()">Print Table</button>

  <script>
    function printTable() {
      var table = document.getElementById("myTable");
      window.print();
    }
  </script>
</body>
</html>
