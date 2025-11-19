document.addEventListener('DOMContentLoaded', function() {
    const printPdfBtn = document.getElementById('printPdfBtn');
    
    if (printPdfBtn) {
        printPdfBtn.addEventListener('click', function() {
            generatePDF();
        });
    }
    
    function generatePDF() {
        // Ambil data tabel
        const table = document.getElementById('dataTable');
        const tableData = getTableData(table);
        
        // Set orientasi halaman ke landscape jika data cukup lebar
        const orientation = tableData.columns.length > 4 ? 'landscape' : 'portrait';
        
        // Buat dokumen PDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: orientation,
            unit: 'mm',
            format: 'a4'
        });
        
        // Tambahkan judul
        doc.setFontSize(16);
        doc.text('LAPORAN DATA MONITORING TANAMAN CABAI', doc.internal.pageSize.getWidth() / 2, 15, {
            align: 'center'
        });
        
        // Tambahkan tanggal cetak
        const today = new Date();
        const dateStr = today.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: 'long', 
            year: 'numeric' 
        });
        
        doc.setFontSize(10);
        doc.text(`Dicetak pada: ${dateStr}`, doc.internal.pageSize.getWidth() - 20, 10, {
            align: 'right'
        });
        
        // Tambahkan tabel menggunakan autotable plugin
        doc.autoTable({
            head: [tableData.columns],
            body: tableData.data,
            startY: 25,
            headStyles: {
                fillColor: [22, 160, 133],
                textColor: [255, 255, 255],
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [242, 242, 242]
            },
            margin: { top: 25 },
            styles: {
                overflow: 'linebreak',
                cellPadding: 3
            },
            columnStyles: {
                0: { cellWidth: 40 }, // Tanggal
                1: { cellWidth: 30 }, // Suhu
                2: { cellWidth: 30 }, // Kelembapan
                3: { cellWidth: 40 }  // Status
            },
            didDrawPage: function(data) {
                // Footer dengan nomor halaman
                const pageCount = doc.internal.getNumberOfPages();
                const currentPage = data.pageNumber;
                
                doc.setFontSize(10);
                doc.text(
                    `Halaman ${currentPage} dari ${pageCount}`,
                    doc.internal.pageSize.getWidth() / 2,
                    doc.internal.pageSize.getHeight() - 10,
                    { align: 'center' }
                );
            }
        });
        
        // Simpan atau tampilkan PDF
        doc.save('Laporan_Monitoring_Cabai_' + formatDate(today) + '.pdf');
    }
    
    function getTableData(table) {
        const headers = [];
        const data = [];
        
        // Get headers, excluding the action column
        const headerRow = table.querySelector('thead tr');
        Array.from(headerRow.cells).forEach((cell, index) => {
            if (!cell.textContent.toLowerCase().includes('aksi')) {
                headers.push(cell.textContent);
            }
        });
        
        // Get visible rows only (for filter/pagination support)
        const rows = Array.from(table.querySelectorAll('tbody tr')).filter(
            row => row.style.display !== 'none'
        );
        
        // Get row data
        rows.forEach(row => {
            const rowData = [];
            
            Array.from(row.cells).forEach((cell, index) => {
                // Skip action column
                if (index < headers.length) {
                    rowData.push(cell.textContent);
                }
            });
            
            data.push(rowData);
        });
        
        return { columns: headers, data: data };
    }
    
    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}${month}${year}`;
    }
});