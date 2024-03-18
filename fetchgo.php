<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Books</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
  <h2>Books</h2>
  <div class="form-group">
    <label for="search">Search by Title:</label>
    <input type="text" class="form-control" id="search" onkeyup="filterBooks()" placeholder="Enter title...">
  </div>
  <div id="total-results"></div> <!-- Total results count will be displayed here -->
  <table id="books-table" class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Author</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

  <nav aria-label="Page navigation">
    <ul id="pagination" class="pagination">
    </ul>
  </nav>
</div>

<script>
  let allBooks = [];
  let currentPage = 1;

  function fetchBooks(page) {
    currentPage = page;
    let searchText = document.getElementById("search").value.trim();
    let api_url = "https://www.googleapis.com/books/v1/volumes?q=" + searchText + "&startIndex=" + ((page - 1) * 10);
    let xhr = new XMLHttpRequest();
    xhr.open("GET", api_url, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let data = JSON.parse(xhr.responseText);
        allBooks = data.items || []; 
        displayBooks(allBooks);
        updatePagination(page, data.totalItems);
        displayTotalResults(data.totalItems); // Display total results count
      }
    };
    xhr.send();
  }

  function displayBooks(books) {
    let tableBody = document.getElementById("books-table").getElementsByTagName("tbody")[0];
    tableBody.innerHTML = "";
    books.forEach(function (item) {
      let title = item.volumeInfo.title || 'N/A';
      let author = item.volumeInfo.authors ? item.volumeInfo.authors[0] : 'N/A';
      tableBody.innerHTML += `<tr><td>${title}</td><td>${author}</td></tr>`;
    });
  }

  function updatePagination(page, totalItems) {
    let pagination = document.getElementById("pagination");
    pagination.innerHTML = "";
    let totalPages = Math.ceil(totalItems / 10);
    
    // Previous Button
    if (page > 1) {
      pagination.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="fetchBooks(${page - 1})">Previous</a></li>`;
    }

    // Numbered Buttons
    let startPage = Math.max(1, page - 5);
    let endPage = Math.min(totalPages, startPage + 9);
    for (let i = startPage; i <= endPage; i++) {
      pagination.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="fetchBooks(${i})">${i}</a></li>`;
    }

    // Next Button
    if (page < totalPages) {
      pagination.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="fetchBooks(${page + 1})">Next</a></li>`;
    }
  }

  function displayTotalResults(totalItems) {
    let totalResultsDiv = document.getElementById("total-results");
    totalResultsDiv.innerHTML = `<p>Total Results: ${totalItems}</p>`;
  }

  // Filter Books based on title
  function filterBooks() {
    let searchText = document.getElementById("search").value.trim();
    fetchBooks(1); // Fetch books again with the new search term
  }

  // Initial load
  fetchBooks(1);
</script>

</body>
</html>
