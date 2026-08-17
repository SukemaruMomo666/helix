import pandas as pd
from openpyxl.styles import Font, Alignment

# Membaca data CSV
df = pd.read_csv('Laporan_Penjualan_Agustus.csv')

# Mengambil tanggal saja (tanpa jam)
df['Tanggal Saja'] = df['Tanggal'].str.split(' ').str[0]
# Mengubah kolom menjadi numerik untuk dijumlahkan
df['Qty'] = pd.to_numeric(df['Qty'])
df['Total Harga'] = pd.to_numeric(df['Total Harga'])

# 1. Rekap Per Hari
rekap_per_hari = df.groupby('Tanggal Saja').agg(
    Total_Item_Terjual=('Qty', 'sum'),
    Total_Pendapatan=('Total Harga', 'sum')
).reset_index()
rekap_per_hari.rename(columns={'Tanggal Saja': 'Tanggal'}, inplace=True)

# 2. Rekap Per Produk
rekap_per_produk = df.groupby('Item').agg(
    Total_Terjual=('Qty', 'sum'),
    Total_Pendapatan=('Total Harga', 'sum')
).reset_index()
rekap_per_produk = rekap_per_produk.sort_values('Total_Terjual', ascending=False)

# 3. Rekap Semua Transaksi (Detail)
rekap_detail = df.copy()

# Menyimpan ke Excel
file_name = 'Laporan_Rekap_Agustus.xlsx'
with pd.ExcelWriter(file_name, engine='openpyxl') as writer:
    rekap_per_hari.to_excel(writer, sheet_name='Rekap Harian', index=False)
    rekap_per_produk.to_excel(writer, sheet_name='Rekap Per Produk', index=False)
    rekap_detail.to_excel(writer, sheet_name='Detail Transaksi', index=False)
    
    # Auto-adjust column width for aesthetics
    for sheetname in writer.sheets:
        worksheet = writer.sheets[sheetname]
        for col in worksheet.columns:
            max_length = 0
            column = col[0].column_letter # Get the column name
            for cell in col:
                try: # Necessary to avoid error on empty cells
                    if len(str(cell.value)) > max_length:
                        max_length = len(str(cell.value))
                except:
                    pass
            adjusted_width = (max_length + 2)
            worksheet.column_dimensions[column].width = adjusted_width
            
            # Format header
            worksheet[column + '1'].font = Font(bold=True)
            worksheet[column + '1'].alignment = Alignment(horizontal="center")

print("Berhasil membuat Laporan_Rekap_Agustus.xlsx")
