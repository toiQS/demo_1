create database `chdidong`;
use `chdidong`;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `chitietgiohang` (
  `idTK` int NOT NULL,
  `idSP` int NOT NULL,
  `SOLUONG` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `chitiethoadon` (
  `idHD` int NOT NULL,
  `idSP` int NOT NULL,
  `SOLUONG` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `chitietkhuyenmai` (
  `idTK` int NOT NULL,
  `idKM` int NOT NULL,
  `SOLUONG` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `chitietphieunhap` (
  `idPN` int NOT NULL,
  `idSP` int NOT NULL,
  `SOLUONG` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `danhmuc` (
  `idDM` int NOT NULL,
  `LOAISP` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `danhmuc` (`idDM`, `LOAISP`) VALUES
(1, 'Điện thoại'),
(2, 'Củ sạc (Adapter)'),
(3, 'Dây sạc'),
(4, 'Ốp lưng'),
(5, 'Tai Nghe'),
(6, 'Đồng hồ'),
(7, 'Tablet'),
(8, 'iPad');

-- --------------------------------------------------------

CREATE TABLE `hang` (
  `idHANG` int NOT NULL,
  `TENHANG` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hang` (`idHANG`, `TENHANG`) VALUES
(1, 'Apple'),
(2, 'Xiaomi'),
(3, 'Samsung'),
(4, 'Oppo'),
(5, 'Sony'),
(6, 'ZMI'),
(7, 'HOCO'),
(8, 'Remax'),
(9, 'Lenovo'),
(10, 'Honor'),
(11, 'TCL'),
(12, 'CITIZEN'),
(13, 'FREDERIQUE CONSTANT'),
(14, 'ORIENT'),
(15, 'G-SHOCK'),
(16, 'KORLEX'),
(17, 'EDOX'),
(18, 'MVW'),
(19, 'SHENZEN'),
(20, 'Hochuen'),
(21, 'HANOI SEOWONINTECH'),
(22, 'Ugreen');

-- --------------------------------------------------------

CREATE TABLE `hoadon` (
  `idHD` int NOT NULL,
  `idTK` int NOT NULL,
  `THANHTIEN` DECIMAL(15, 0) NOT NULL,
  `NGAYMUA` date NOT NULL,
  `DIACHI` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `MAKHUYENMAI` int NOT NULL,
  `TRANGTHAI` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idTHANHTOAN` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `khuyenmai` (
  `MAKHUYENMAI` int NOT NULL,
  `CODE` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `GIATRI` decimal(5,2) NOT NULL,
  `SOLUONG` int NOT NULL DEFAULT 0,
  `NGAYAPDUNG` date NOT NULL,
  `HANSUDUNG` date NOT NULL,
  `TRANGTHAI` tinyint NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `khuyenmai` (`MAKHUYENMAI`, `CODE`, `GIATRI`, `SOLUONG`, `NGAYAPDUNG`, `HANSUDUNG`, `TRANGTHAI`) VALUES
(1, 'SALE10', 0.10, 100, '2026-01-01', '2026-12-31', 0),
(2, 'SALE20', 0.20, 50, '2026-02-01', '2026-06-30', 0),
(3, 'DISCOUNT15', 0.15, 30, '2026-03-01', '2026-07-31', 1),
(4, 'NEWYEAR25', 0.25, 20, '2026-04-01', '2026-05-31', 1),
(5, 'SUMMER30', 0.30, 10, '2026-05-01', '2026-09-30', 1);

-- --------------------------------------------------------

CREATE TABLE `nhacungcap` (
  `idNCC` int NOT NULL,
  `TENNCC` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `SDT` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `DIACHI` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `TRANGTHAI` tinyint NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `phanquyen` (
  `idType` int NOT NULL,
  `idQuyen` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `phieunhap` (
  `idPN` int NOT NULL,
  `idNCC` int NOT NULL,
  `NGAYNHAP` date NOT NULL DEFAULT (CURRENT_DATE), 
  `LANNHAP` int NOT NULL DEFAULT 1,
  `THANHTIEN` DECIMAL(15, 0) NOT NULL,
  `TRANGTHAI` tinyint NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `ptthanhtoan` (
  `idThanhToan` int NOT NULL,
  `TENPHUONGTHUC` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ptthanhtoan` (`idThanhToan`, `TENPHUONGTHUC`) VALUES
(1, 'Thanh toán tiền mặt'),
(2, 'Chuyển Khoản'),
(3, 'Thanh toán trực tuyến');

-- --------------------------------------------------------

CREATE TABLE `sanpham` (
  `idSP` int NOT NULL,
  `TENSP` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `HANG` int NOT NULL,
  `GIANHAP` decimal(15, 0) NOT NULL,
  `LOINHUAN` decimal(5,2) NOT NULL,
  `GIABAN` decimal(15, 0) NOT NULL,
  `DVTINH` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'VNĐ',
  `SOLUONG` int NOT NULL DEFAULT 0,
  `idDM` int NOT NULL,
  `IMG` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `MOTA` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `TRANGTHAI` tinyint NOT NULL DEFAULT 1,
  `DISCOUNT` int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sanpham` (`idSP`, `TENSP`, `HANG`, `GIANHAP`, `LOINHUAN`, `GIABAN`, `DVTINH`, `SOLUONG`, `idDM`, `IMG`, `MOTA`, `TRANGTHAI`, `DISCOUNT`) VALUES
(1, 'iPhone 16', 1, 19200000, 25.00, 24000000, 'VNĐ', 0, 1, 'Iphone 16.jpeg', 'Hiệu năng vượt trội...', 1, 10),
(2, 'iPhone 16 plus', 1, 28000000, 25.00, 35000000, 'VNĐ', 0, 1, 'Iphone 16 pờ lếch.jpeg', 'iPhone 16 Plus...', 1, 10),
(3, 'SamSung Galaxy Z Flip 6', 3, 19120000, 25.00, 23900000, 'VNĐ', 0, 1, 'SamSung Galaxy Z Flip 6.jpeg', 'Galaxy Z Flip6...', 1, 10),
(4, 'iPhone 16 Ultra', 1, 26232000, 25.00, 32790000, 'VNĐ', 0, 1, 'iphone 16 Pro Max.jpeg', 'iPhone 16 Plus...', 1, 10),
(5, 'Airpods pro 2', 1, 4952000, 25.00, 6190000, 'VNĐ', 1, 5, 'Airpods pro 2.jpg', 'Trải nghiệm...', 1, 10),
(6, 'Samsung Galaxy S21', 3, 6399200, 25.00, 7999000, 'VNĐ', 0, 1, 'Samsung Galaxy S21.jpg', 'Smartphone Samsung S21', 1, 10),
(7, 'iPhone 13', 1, 15200000, 25.00, 19000000, 'VNĐ', 1, 1, 'iphone 13.jpeg', 'Smartphone iPhone 13', 1, 10),
(8, 'Sony-1000XM4-Gold-A', 5, 2400000, 25.00, 3000000, 'VNĐ', 0, 5, 'Sony-1000XM4-Gold-A.jpg', 'Tai nghe Sony chống ồn', 1, 10),
(9, 'Củ sạc Xiaomi', 2, 119200, 25.00, 149000, 'VNĐ', 0, 2, 'Cu-Sac-Nhanh-Type-C-20W-Xiaomi-AD201-Quoc-Te-chinh-hang-mi360-3.jpg', 'Củ sạc nhanh...', 1, 10),
(10, 'Củ sạc Samsung', 3, 47200, 25.00, 59000, 'VNĐ', 0, 2, 'cu-sac-samsung-mi360.jpg', '– Củ sạc nhanh...', 1, 0),
(11, 'Máy tính bảng TCL Tab 10L Gen 3', 11, 2152000, 25.00, 2690000, 'VNĐ', 0, 7, 'Máy tính bảng TCL Tab 10L Gen 3.jpg', 'TCL Tab 10L Gen 3...', 1, 0),
(12, 'Máy tính bảng TCL Tab 10L Gen 2', 11, 1592000, 25.00, 1990000, 'VNĐ', 0, 7, 'Máy tính bảng TCL Tab 10L Gen 2.jpg', 'Được ra mắt...', 1, 0),
(13, 'Máy tính bảng Samsung Galaxy Tab S10 Ultra', 3, 19432000, 25.00, 24290000, 'VNĐ', 0, 7, 'Máy tính bảng Samsung Galaxy Tab S10 Ultra.jpg', 'Samsung Galaxy Tab...', 1, 0),
(14, 'Máy tính bảng Samsung Galaxy Tab A9+ 5G', 3, 4792000, 25.00, 5990000, 'VNĐ', 0, 7, 'Máy tính bảng Samsung Galaxy Tab A9+ 5G.jpg', 'Với giá cả...', 1, 0),
(15, 'Máy tính bảng Samsung Galaxy Tab S10+', 3, 15432000, 25.00, 19290000, 'VNĐ', 0, 7, 'Máy tính bảng Samsung Galaxy Tab S10+.jpg', 'Samsung tiếp tục...', 1, 0),
(16, 'Máy tính bảng Samsung Galaxy Tab S10 Ultra 5G', 3, 21832000, 25.00, 27290000, 'VNĐ', 0, 7, 'Máy tính bảng Samsung Galaxy Tab S10 Ultra 5G.jpg', 'Samsung Galaxy Tab...', 1, 0),
(17, 'Máy tính bảng Lenovo Tab Plus', 9, 5352000, 25.00, 6690000, 'VNĐ', 0, 7, 'Máy tính bảng Lenovo Tab Plus.jpg', 'Lenovo Tab Plus...', 1, 0),
(18, 'Máy tính bảng Lenovo Tab M9', 9, 2072000, 25.00, 2590000, 'VNĐ', 0, 7, 'Máy tính bảng Lenovo Tab M9.jpg', 'Để mở rộng...', 1, 0),
(19, 'Cáp sạc Type C Zmi AL303-AL873', 6, 143200, 25.00, 179000, 'VNĐ', 0, 3, 'Cáp sạc Type C Zmi AL303-AL873.jpg', 'Bạn đang tìm...', 1, 0),
(20, 'Cáp sạc Type C ZMI AL706', 6, 159200, 25.00, 199000, 'VNĐ', 0, 3, 'Cap-type-C-sieu-ben-Xiaomi-ZMI-AL706-chinh-hang-mi360.jpg', 'Cáp sạc Type C...', 1, 0),
(21, 'Củ sạc nhanh Zmi HA612', 2, 79200, 25.00, 99000, 'VNĐ', 0, 2, 'Cu-Sac-Nhanh-Xiaomi-Zmi-HA716-chinh-hang-mi360-3.png', 'Bạn đang tìm...', 1, 0),
(22, 'Củ sạc nhanh HOCO 3USB HK1', 7, 132000, 25.00, 165000, 'VNĐ', 0, 2, 'Củ sạc nhanh HOCO 3USB HK1.png', 'Củ sạc nhanh...', 1, 0),
(23, 'Củ sạc nhanh Xiaomi AD332EU', 2, 199200, 25.00, 249000, 'VNĐ', 0, 2, 'Củ sạc nhanh Xiaomi AD332EU.jpg', 'Củ sạc nhanh...', 1, 0),
(24, 'Củ sạc nhanh Zmi 1A1C HA722', 6, 183200, 25.00, 229000, 'VNĐ', 0, 2, 'Củ sạc nhanh Zmi 1A1C HA722.jpg', 'Củ sạc nhanh...', 1, 0),
(25, 'Tai nghe Bluetooth Business Remax RB T15', 8, 199200, 25.00, 249000, 'VNĐ', 0, 5, 'Tai nghe Bluetooth Business Remax RB T15.jpg', 'Tai nghe Bluetooth...', 1, 0),
(26, 'Tai nghe In-Ear Headphones Basic', 2, 119200, 25.00, 149000, 'VNĐ', 0, 5, 'Tai nghe In-Ear Headphones Basic.jpg', 'Tai nghe In-Ear...', 1, 0);

-- --------------------------------------------------------

CREATE TABLE `taikhoan` (
  `idTK` int NOT NULL,
  `USERNAME` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `PASSWORD` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `SDT` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `EMAIL` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ADDRESS` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `HOTEN` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `PHANLOAI` int NOT NULL,
  `TRANGTHAI` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `usertype` (
  `idType` int NOT NULL,
  `Ten` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usertype` (`idType`, `Ten`) VALUES
(1, 'Khách hàng'),
(2, 'Quản lý');

-- Indexes & Constraints
ALTER TABLE `chitietgiohang` ADD KEY `tk-ctgh` (`idTK`), ADD KEY `sp-ctgh` (`idSP`);
ALTER TABLE `chitiethoadon` ADD KEY `hd-cthd` (`idHD`), ADD KEY `sp-cthd` (`idSP`);
ALTER TABLE `chitietkhuyenmai` ADD KEY `tk-ctkm` (`idTK`), ADD KEY `km-ctkm` (`idKM`);
ALTER TABLE `chitietphieunhap` ADD KEY `pn-ctpn` (`idPN`), ADD KEY `sp-ctpn` (`idSP`);
ALTER TABLE `danhmuc` ADD PRIMARY KEY (`idDM`);
ALTER TABLE `hang` ADD PRIMARY KEY (`idHANG`);
ALTER TABLE `hoadon` ADD PRIMARY KEY (`idHD`), ADD KEY `TK-HD` (`idTK`), ADD KEY `KM-HD` (`MAKHUYENMAI`), ADD KEY `tt-hd` (`idTHANHTOAN`);
ALTER TABLE `khuyenmai` ADD PRIMARY KEY (`MAKHUYENMAI`);
ALTER TABLE `nhacungcap` ADD PRIMARY KEY (`idNCC`);
ALTER TABLE `phanquyen` ADD KEY `type-pq` (`idType`), ADD KEY `tt-pq` (`idQuyen`);
ALTER TABLE `phieunhap` ADD PRIMARY KEY (`idPN`), ADD KEY `ncc-pn` (`idNCC`);
ALTER TABLE `ptthanhtoan` ADD PRIMARY KEY (`idThanhToan`);
ALTER TABLE `sanpham` ADD PRIMARY KEY (`idSP`), ADD KEY `DM-SP` (`idDM`), ADD KEY `Hang` (`HANG`);
ALTER TABLE `taikhoan` ADD PRIMARY KEY (`idTK`), ADD KEY `PQ-TK` (`PHANLOAI`);
ALTER TABLE `usertype` ADD PRIMARY KEY (`idType`);

ALTER TABLE `danhmuc` MODIFY `idDM` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `hang` MODIFY `idHANG` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
ALTER TABLE `hoadon` MODIFY `idHD` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `khuyenmai` MODIFY `MAKHUYENMAI` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `nhacungcap` MODIFY `idNCC` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `phieunhap` MODIFY `idPN` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `ptthanhtoan` MODIFY `idThanhToan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `sanpham` MODIFY `idSP` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
ALTER TABLE `taikhoan` MODIFY `idTK` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `usertype` MODIFY `idType` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `taikhoan` ADD COLUMN `remember_token` VARCHAR(255) NULL DEFAULT NULL, ADD COLUMN `token_expiry` DATETIME NULL DEFAULT NULL;

ALTER TABLE `chitietgiohang` ADD CONSTRAINT `sp-ctgh_fk` FOREIGN KEY (`idSP`) REFERENCES `sanpham` (`idSP`), ADD CONSTRAINT `tk-ctgh_fk` FOREIGN KEY (`idTK`) REFERENCES `taikhoan` (`idTK`);
ALTER TABLE `chitiethoadon` ADD CONSTRAINT `hd-cthd_fk` FOREIGN KEY (`idHD`) REFERENCES `hoadon` (`idHD`), ADD CONSTRAINT `sp-cthd_fk` FOREIGN KEY (`idSP`) REFERENCES `sanpham` (`idSP`);
ALTER TABLE `chitietkhuyenmai` ADD CONSTRAINT `km-ctkm_fk` FOREIGN KEY (`idKM`) REFERENCES `khuyenmai` (`MAKHUYENMAI`), ADD CONSTRAINT `tk-ctkm_fk` FOREIGN KEY (`idTK`) REFERENCES `taikhoan` (`idTK`);
ALTER TABLE `chitietphieunhap` ADD CONSTRAINT `pn-ctpn_fk` FOREIGN KEY (`idPN`) REFERENCES `phieunhap` (`idPN`), ADD CONSTRAINT `sp-ctpn_fk` FOREIGN KEY (`idSP`) REFERENCES `sanpham` (`idSP`);
ALTER TABLE `hoadon` ADD CONSTRAINT `KM-HD_fk` FOREIGN KEY (`MAKHUYENMAI`) REFERENCES `khuyenmai` (`MAKHUYENMAI`), ADD CONSTRAINT `TK-HD_fk` FOREIGN KEY (`idTK`) REFERENCES `taikhoan` (`idTK`), ADD CONSTRAINT `tt-hd_fk` FOREIGN KEY (`idTHANHTOAN`) REFERENCES `ptthanhtoan` (`idThanhToan`);
ALTER TABLE `phanquyen` ADD CONSTRAINT `type-pq_fk` FOREIGN KEY (`idType`) REFERENCES `usertype` (`idType`);
ALTER TABLE `phieunhap` ADD CONSTRAINT `ncc-pn_fk` FOREIGN KEY (`idNCC`) REFERENCES `nhacungcap` (`idNCC`);
ALTER TABLE `sanpham` ADD CONSTRAINT `DM-SP_fk` FOREIGN KEY (`idDM`) REFERENCES `danhmuc` (`idDM`), ADD CONSTRAINT `Hang_fk` FOREIGN KEY (`HANG`) REFERENCES `hang` (`idHANG`);
ALTER TABLE `taikhoan` ADD CONSTRAINT `PQ-TK_fk` FOREIGN KEY (`PHANLOAI`) REFERENCES `usertype` (`idType`);

-- --------------------------------------------------------
CREATE TABLE `cauhinh_canhbao` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `idND` INT NOT NULL,         
  `idSP` INT NOT NULL,          
  `NGUONG_DAT` INT NOT NULL,    
  `NGAY_CAP_NHAT` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`idSP`) REFERENCES `sanpham`(`idSP`),
  FOREIGN KEY (`idND`) REFERENCES `taikhoan`(`idTK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;