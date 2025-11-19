document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const dashboardButton = document.getElementById('dashboardButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                toggleSidebar.innerHTML = '▶';
            } else {
                toggleSidebar.innerHTML = '◀';
            }
        });
    }

    if (dashboardButton) {
        dashboardButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
    }

    // Table functionality
    const dataTable = document.getElementById('dataTable');
    if (dataTable) {
        const searchInput = document.getElementById('searchInput');
        const entriesSelect = document.getElementById('entriesSelect');
        const tableInfo = document.getElementById('tableInfo');
        const pagination = document.getElementById('pagination');
        
        let currentPage = 1;
        let rowsPerPage = parseInt(entriesSelect.value);
        let filteredRows = [...document.querySelectorAll('#dataTable tbody tr')];
        
        // Initialize table
        updateTable();
        
        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filteredRows = [...document.querySelectorAll('#dataTable tbody tr')].filter(row => {
                return Array.from(row.cells).some(cell => 
                    cell.textContent.toLowerCase().includes(searchTerm)
                );
            });
            currentPage = 1;
            updateTable();
        });
        
        // Change entries per page
        entriesSelect.addEventListener('change', function() {
            rowsPerPage = parseInt(this.value);
            currentPage = 1;
            updateTable();
        });
        
        // Sorting functionality
        document.querySelectorAll('#dataTable th[data-sort]').forEach(th => {
            th.addEventListener('click', function() {
                const sortBy = this.getAttribute('data-sort');
                const headers = document.querySelectorAll('#dataTable th');
                
                // Remove existing sort indicators
                headers.forEach(header => {
                    header.classList.remove('sort-asc', 'sort-desc');
                });
                
                let sortDirection;
                if (this.classList.contains('sort-asc')) {
                    this.classList.remove('sort-asc');
                    this.classList.add('sort-desc');
                    sortDirection = 'desc';
                } else {
                    headers.forEach(header => header.classList.remove('sort-asc', 'sort-desc'));
                    this.classList.add('sort-asc');
                    sortDirection = 'asc';
                }
                
                // Sort rows
                sortTableByColumn(sortBy, sortDirection);
                updateTable();
            });
        });
        
        // Function to update table display
        function updateTable() {
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            // Show current page rows
            const tbody = document.querySelector('#dataTable tbody');
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = Math.min(startIndex + rowsPerPage, totalRows);
            
            // Hide all rows
            filteredRows.forEach(row => row.style.display = 'none');
            
            // Show relevant rows
            for (let i = startIndex; i < endIndex; i++) {
                if (filteredRows[i]) {
                    filteredRows[i].style.display = '';
                }
            }
            
            // Update info text
            if (tableInfo) {
                if (totalRows === 0) {
                    tableInfo.textContent = 'Tidak ada data yang ditampilkan';
                } else {
                    tableInfo.textContent = `Menampilkan ${startIndex + 1} sampai ${endIndex} dari ${totalRows} data`;
                }
            }
            
            // Update pagination
            if (pagination) {
                createPagination(totalPages);
            }
        }
        
        // Create pagination links
        function createPagination(totalPages) {
            pagination.innerHTML = '';
            
            if (totalPages <= 1) return;
            
            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.innerHTML = '&laquo;';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateTable();
                }
            });
            pagination.appendChild(prevBtn);
            
            // Page numbers
            const maxPages = 5; // Max pages to show
            let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
            let endPage = Math.min(totalPages, startPage + maxPages - 1);
            
            if (endPage - startPage + 1 < maxPages) {
                startPage = Math.max(1, endPage - maxPages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.classList.toggle('active', i === currentPage);
                pageBtn.addEventListener('click', () => {
                    currentPage = i;
                    updateTable();
                });
                pagination.appendChild(pageBtn);
            }
            
            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.innerHTML = '&raquo;';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    updateTable();
                }
            });
            pagination.appendChild(nextBtn);
        }
        
        // Sort table
        function sortTableByColumn(column, direction) {
            const compareFn = (a, b) => {
                let valueA, valueB;
                
                // Get column index
                const headers = Array.from(document.querySelectorAll('#dataTable th'));
                const columnIndex = headers.findIndex(th => th.getAttribute('data-sort') === column);
                
                if (columnIndex === -1) return 0;
                
                valueA = a.cells[columnIndex].textContent.trim();
                valueB = b.cells[columnIndex].textContent.trim();
                
                // Handle different column types
                if (column === 'date') {
                    // Convert DD/MM/YYYY HH:MM to sortable date
                    valueA = convertDateStringToDate(valueA);
                    valueB = convertDateStringToDate(valueB);
                } else if (column === 'suhu' || column === 'kelembapan') {
                    // Convert to number
                    valueA = parseFloat(valueA);
                    valueB = parseFloat(valueB);
                }
                
                // Compare based on direction
                if (direction === 'asc') {
                    return valueA > valueB ? 1 : -1;
                } else {
                    return valueA < valueB ? 1 : -1;
                }
            };
            
            filteredRows.sort(compareFn);
        }
        
        // Helper function to convert DD/MM/YYYY HH:MM to Date object
        function convertDateStringToDate(dateString) {
            // Parse DD/MM/YYYY HH:MM format
            const parts = dateString.split(' ');
            const dateParts = parts[0].split('/');
            const day = parseInt(dateParts[0]);
            const month = parseInt(dateParts[1]) - 1; // Months are 0-indexed
            const year = parseInt(dateParts[2]);
            
            if (parts.length > 1) {
                const timeParts = parts[1].split(':');
                const hour = parseInt(timeParts[0]);
                const minute = parseInt(timeParts[1]);
                return new Date(year, month, day, hour, minute);
            }
            
            return new Date(year, month, day);
        }
    }
});