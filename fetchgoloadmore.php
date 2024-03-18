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
    <input type="text" class="form-control" id="search" onkeyup="filterBooks(event)" placeholder="Enter title...">
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

  <button id="load-more-btn" class="btn btn-primary mt-3" style="display:none;">Load More</button>

</div>

<script>
  let allBooks = [];
  let currentPage = 1;
  let totalItems = 0;

  function fetchBooks(page) {
    currentPage = page;
    let searchText = document.getElementById("search").value.trim();
    let startIndex = (page - 1) * 10;
    let api_url = "https://www.googleapis.com/books/v1/volumes?q=" + encodeURIComponent(searchText) + "&startIndex=" + startIndex;
    let xhr = new XMLHttpRequest();
    xhr.open("GET", api_url, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let data = JSON.parse(xhr.responseText);
        totalItems = data.totalItems;
        allBooks = data.items || [];
        displayBooks(allBooks);
        updatePagination(page, totalItems);
        displayTotalResults(totalItems); // Display total results count
        document.getElementById("load-more-btn").style.display = "block"; // Show load more button
      }
    };
    xhr.send();
  }

  function displayBooks(books) {
    let tableBody = document.getElementById("books-table").getElementsByTagName("tbody")[0];
    books.forEach(function (item) {
      let title = item.volumeInfo.title || 'N/A';
      let author = item.volumeInfo.authors ? item.volumeInfo.authors[0] : 'N/A';
      tableBody.innerHTML += `<tr><td>${title}</td><td>${author}</td></tr>`;
    });
  }

  function updatePagination(page, totalItems) {
    let totalPages = Math.ceil(totalItems / 10);

    // Next Button
    if (page < totalPages) {
      document.getElementById("load-more-btn").style.display = "block";
    } else {
      document.getElementById("load-more-btn").style.display = "none";
    }
  }

  function displayTotalResults(totalItems) {
    let totalResultsDiv = document.getElementById("total-results");
    totalResultsDiv.innerHTML = `<p>Total Results: ${totalItems}</p>`;
  }

  // Filter Books based on title
  function filterBooks(event) {
    if (event.keyCode === 13) { // Check if Enter key is pressed
      currentPage = 1; // Reset current page when filtering
      fetchBooks(1); // Fetch books again with the new search term
    }
  }

  // Load more functionality
  document.getElementById("load-more-btn").addEventListener("click", function() {
    let searchText = document.getElementById("search").value.trim();
    let startIndex = currentPage * 10;
    let api_url = "https://www.googleapis.com/books/v1/volumes?q=" + encodeURIComponent(searchText) + "&startIndex=" + startIndex;
    let xhr = new XMLHttpRequest();
    xhr.open("GET", api_url, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let data = JSON.parse(xhr.responseText);
        allBooks = allBooks.concat(data.items || []);
        displayBooks(data.items);
        currentPage++;
        updatePagination(currentPage, totalItems);
      }
    };
    xhr.send();
  });

</script>

</body>
</html>
