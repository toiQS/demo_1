<?php

// binhluan - chưa có trong schema, dùng tinyint chuẩn
enum trang_thai_binh_luan: int {
    case AN       = 0; // Ẩn
    case HIEN_THI = 1; // Hiển thị
}

// danhmuc - chưa có TRANGTHAI trong schema, dùng tinyint chuẩn
enum trang_thai_danh_muc: int {
    case INACTIVE = 0;
    case ACTIVE   = 1;
}

// dvvanchuyen - chưa có trong schema, dùng tinyint chuẩn
enum trang_thai_dich_vu_van_chuyen: int {
    case INACTIVE = 0;
    case ACTIVE   = 1;
}

// hang - chưa có TRANGTHAI trong schema, dùng tinyint chuẩn
enum trang_thai_hang: int {
    case INACTIVE = 0;
    case ACTIVE   = 1;
}

// hoadon - TRANGTHAI varchar(20)
enum trang_thai_hoa_don: string {
    case PENDING    = 'pending';    // Chờ xác nhận
    case PROCESSING = 'processing'; // Đang xử lý
    case SHIPPED    = 'shipped';    // Đang giao
    case COMPLETED  = 'completed';  // Hoàn thành
    case CANCELLED  = 'cancelled';  // Đã huỷ
}

// khuyenmai - TRANGTHAI tinyint DEFAULT 0
enum trang_thai_khuyen_mai: int {
    case INACTIVE = 0; // Chưa áp dụng
    case ACTIVE   = 1; // Đang áp dụng
}

// nhacungcap - TRANGTHAI tinyint DEFAULT 1
enum trang_thai_nha_cung_cap: int {
    case INACTIVE = 0; // Ngừng hợp tác
    case ACTIVE   = 1; // Đang hợp tác
}

// phieunhap - TRANGTHAI tinyint DEFAULT 1
enum trang_thai_phieu_nhap: int {
    case HUY      = 0; // Đã huỷ
    case HOAN_TAT = 1; // Hoàn tất
}

// sanpham - TRANGTHAI tinyint DEFAULT 1
enum trang_thai_san_pham: int {
    case INACTIVE = 0; // Ngừng bán
    case ACTIVE   = 1; // Đang bán
}

// taikhoan - TRANGTHAI tinyint(1) DEFAULT 1
enum trang_thai_tai_khoan: int {
    case BI_KHOA   = 0; // Bị khoá
    case HOAT_DONG = 1; // Hoạt động
}

// thaotac - chưa có trong schema, dùng tinyint chuẩn
enum trang_thai_thao_tac: int {
    case INACTIVE = 0;
    case ACTIVE   = 1;
}

// usertype - idType: 1 = Khách hàng, 2 = Quản lý
enum trang_thai_loai_tai_khoan: int {
    case KHACH_HANG = 1; // Khách hàng
    case QUAN_LY    = 2; // Quản lý
}