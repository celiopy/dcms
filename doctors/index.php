<?php
    require_once ('functions.php');

	function changeDb($db = 1) {
		$array = [
			1 => 'dms', 
			2 => 'dms_cap'
		];

		return new Database('localhost', 'root', 'tsu', $array[$db]);
	}

    $db = changeDb(isset($_COOKIE['unitid']) ? $_COOKIE['unitid'] : 1);

    $user_id = User::is_logged_in($db);

    if ($user_id === false) {
        header('Location: ' . BASEURL . 'login.php');
        exit;
    }

    $title = "Doutores - " . $title;
    include (ABSPATH . '_partials/header.html');
?>
    <main class="main">
        <div class="flex items-center gap-300" style="margin-block-end: 2rem;">
            <i class="mi mi-doctor" style="display: flex; align-items: center; justify-content: center; width: 1.5em; height: 1.5em; border: 1px solid; border-radius: 100%; font-size: 2rem;"></i>
            <h2>Doutores</h2>
        </div>

        <!-- HTML code for the search form and search results container -->
        <form id="search-form">
            <input type="text" name="query" placeholder="Search">
            <noscript><button type="submit">Search</button></noscript>
        </form>

        <div id="search-results"></div>
        <div id="search-counter">f</div>

        <!-- HTML code for the "Load more" button -->
        <button id="load-more-btn">Load more</button>
    </main>

    <script>
        // JavaScript code for the search form and load more button

        const searchForm = document.querySelector('#search-form');
        const queryInput = searchForm.querySelector('input');
        const searchResults = document.querySelector('#search-results');
        const searchCounter = document.querySelector('#search-counter');
        const loadMoreBtn = document.querySelector('#load-more-btn');

        let currentPage = 1;
        let totalResults = 1;
        let limit = 3;

        function search() {
            fetch(`find.php?page=${currentPage}&limit=${limit}&query=${queryInput.value}`)
            .then(response => response.json())
            .then(data => {
                // Append the new results to the results div
                searchResults.innerHTML += data.results.map(patient => {
                return `
                    <div class="patient | row row--align-middle">
                        <div>${patient.MatID}</div>
                        <div>${patient.Name}</div>
                        <div>${patient.CPF}</div>
                    </div>
                `;
                }).join('');

                // Update the total results and current display
                const startResult = (currentPage - 1) * limit + 1;
                const endResult = startResult + data.results.length - 1;
                // searchCounter.innerHTML = `${startResult}-${endResult}`;

                searchCounter.textContent = `${data.totalResults} resultados encontrados.`;

                loadMoreBtn.disabled = false;

                // Disable the "load more" button if we've reached the last page
                if (data.totalResults <= endResult) {
                    loadMoreBtn.disabled = true;
                }
            })
            .catch(error => {
                console.error(error);
            });
        }

        search();

        queryInput.addEventListener('input', () => {
            currentPage = 1;
            searchResults.innerHTML = "";
            search();
        });

        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            search();
        });
    </script>
<?php include (ABSPATH . '_partials/footer.html'); ?>