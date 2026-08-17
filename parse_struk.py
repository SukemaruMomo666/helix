import csv
from bs4 import BeautifulSoup
import sys

def parse_html_to_csv(html_file, csv_file):
    with open(html_file, 'r', encoding='utf-16', errors='ignore') as f:
        html_content = f.read()

    soup = BeautifulSoup(html_content, 'html.parser')
    receipts = soup.find_all('div', class_='receipt')

    data = []
    
    # Headers
    data.append(['No. Struk', 'Tanggal', 'Toko', 'Kasir', 'Item', 'Qty', 'Harga Satuan', 'Total Harga'])

    for receipt in receipts:
        # Extract Store Name
        store_name_divs = receipt.find_all('div', class_='store-name')
        toko = "Toko Tidak Diketahui"
        if len(store_name_divs) >= 2:
            toko = store_name_divs[1].text.strip()
        
        # Extract Invoice Info
        invoice_info = receipt.find('div', class_='invoice-info')
        no_struk = ""
        tanggal = ""
        kasir = ""
        
        if invoice_info:
            rows = invoice_info.find_all('tr')
            for row in rows:
                cols = row.find_all('td')
                if len(cols) == 3:
                    label = cols[0].text.strip()
                    value = cols[2].text.strip()
                    if label == 'No. Struk':
                        no_struk = value
                    elif label == 'Tanggal':
                        tanggal = value
                    elif label == 'Kasir':
                        kasir = value
                        
        # Extract Items
        tables = receipt.find_all('table', class_='table-items')
        if not tables:
            continue
            
        items_table = tables[0]
        item_rows = items_table.find_all('tr')
        
        current_item_name = ""
        for row in item_rows:
            cols = row.find_all('td')
            if len(cols) == 1 and cols[0].has_attr('colspan'):
                current_item_name = cols[0].text.strip()
            elif len(cols) == 2:
                qty_price_str = cols[0].text.strip()
                total_str = cols[1].text.strip().replace('.', '').replace('Rp', '').strip()
                
                if 'x' in qty_price_str:
                    qty_parts = qty_price_str.split('x')
                    qty = qty_parts[0].strip()
                    harga_satuan = qty_parts[1].strip().replace('.', '')
                    
                    data.append([no_struk, tanggal, toko, kasir, current_item_name, qty, harga_satuan, total_str])

    with open(csv_file, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerows(data)
        
    print(f"Berhasil mengekstrak {len(data)-1} baris item ke {csv_file}")

if __name__ == '__main__':
    parse_html_to_csv('struk_agustus.html', 'Laporan_Penjualan_Agustus.csv')
