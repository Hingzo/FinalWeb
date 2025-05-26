-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 26, 2025 lúc 04:08 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `legicaft`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_chitietdonhang`
--

CREATE TABLE `tbl_chitietdonhang` (
  `id_chitietdonhang` int(11) NOT NULL,
  `id_donhang` int(11) DEFAULT NULL,
  `id_sanpham` int(11) DEFAULT NULL,
  `soluong` int(11) NOT NULL,
  `giatien` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_chitietdonhang`
--

INSERT INTO `tbl_chitietdonhang` (`id_chitietdonhang`, `id_donhang`, `id_sanpham`, `soluong`, `giatien`) VALUES
(1, 1, 1, 1, 120000.00),
(2, 1, 5, 1, 200000.00),
(3, 2, 2, 1, 90000.00),
(4, 2, 8, 1, 230000.00),
(5, 2, 10, 1, 130000.00),
(6, 3, 3, 1, 115000.00),
(7, 3, 6, 1, 220000.00),
(8, 4, 4, 1, 150000.00),
(9, 4, 7, 1, 180000.00),
(10, 4, 9, 1, 250000.00),
(11, 5, 2, 1, 150000.00),
(12, 6, 1, 1, 270000.00),
(13, 7, 5, 1, 310000.00),
(14, 8, 7, 1, 220000.00),
(15, 9, 4, 1, 395000.00),
(16, 10, 8, 1, 425000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_danhmuc`
--

CREATE TABLE `tbl_danhmuc` (
  `id_danhmuc` int(11) NOT NULL,
  `tendanhmuc` varchar(100) NOT NULL,
  `soluong` int(11) DEFAULT 0,
  `mota` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_danhmuc`
--

INSERT INTO `tbl_danhmuc` (`id_danhmuc`, `tendanhmuc`, `soluong`, `mota`) VALUES
(1, 'HotWheels', 15, 'Xe mô hình từ Hot Wheels, Mini GT, TarmaWorks với tỷ lệ chi tiết'),
(2, 'Mini GT\r\n', 10, 'mô hình xe đồ chơi'),
(3, 'BabyCry', 15, 'mô hình đồ chơi'),
(4, 'Labubu', 10, 'Đồ chơi blind box như Cry Baby, Labubu, Baby Three với nhân vật ngẫu nhiên'),
(5, 'TARMACWORKS', 8, 'Xe mô hình từ TARMACWORKS với tỷ lệ chi tiết'),
(6, 'Baby Three', 5, 'Đồ chơi mini dễ thương baby three');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_donhang`
--

CREATE TABLE `tbl_donhang` (
  `id_donhang` int(11) NOT NULL,
  `ngaylap` date DEFAULT NULL,
  `tongtien` decimal(10,2) DEFAULT 0.00,
  `tinhtrang` varchar(50) DEFAULT NULL,
  `ghichu` text DEFAULT NULL,
  `id_nguoidung` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_donhang`
--

INSERT INTO `tbl_donhang` (`id_donhang`, `ngaylap`, `tongtien`, `tinhtrang`, `ghichu`, `id_nguoidung`) VALUES
(1, '2025-05-15', 320000.00, 'Dang xu ly', 'Giao trong ngày', 1),
(2, '2025-05-16', 450000.00, 'Dang giao hang', 'Giao hàng nhanh', 1),
(3, '2025-05-17', 335000.00, 'Dang xu ly', 'Giao hàng nhanh', 9),
(4, '2025-05-18', 580000.00, 'Cho xac nhan', 'Thêm quà tặng nếu có', 1),
(5, '2025-01-10', 150000.00, 'Dang xu ly', 'Giao sớm nếu được', 10),
(6, '2025-02-14', 270000.00, 'Cho xac nhan', 'Tặng kèm hoa Valentine', 9),
(7, '2025-03-22', 310000.00, 'Dang giao hang', 'Ưu tiên giao buổi chiều', 1),
(8, '2025-04-05', 220000.00, 'Dang xu ly', 'Liên hệ trước khi giao', 9),
(9, '2025-06-12', 395000.00, 'Cho xac nhan', 'Giao hàng đúng giờ', 1),
(10, '2025-07-08', 425000.00, 'Dang xu ly', 'Miễn phí vận chuyển', 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_giohang`
--

CREATE TABLE `tbl_giohang` (
  `id_giohang` int(11) NOT NULL,
  `danhsachsanpham` text DEFAULT NULL,
  `tongtien` decimal(10,2) DEFAULT 0.00,
  `id_nguoidung` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_giohang`
--

INSERT INTO `tbl_giohang` (`id_giohang`, `danhsachsanpham`, `tongtien`, `id_nguoidung`) VALUES
(1, 'Blind Box Series 1 (1 cái)', 150000.00, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_nguoidung`
--

CREATE TABLE `tbl_nguoidung` (
  `id_nguoidung` int(11) NOT NULL,
  `hoten` varchar(100) NOT NULL,
  `gioitinh` varchar(10) DEFAULT NULL,
  `sdt` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `matkhau` varchar(255) NOT NULL,
  `diachi` text DEFAULT NULL,
  `vaitro` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_nguoidung`
--

INSERT INTO `tbl_nguoidung` (`id_nguoidung`, `hoten`, `gioitinh`, `sdt`, `email`, `matkhau`, `diachi`, `vaitro`) VALUES
(1, 'Le Nguyen Mai Quynh', 'Nam', '0901234567', 'quynh@gmail.com', 'a94a8fe5ccb19ba61c4c0873d391e987', '123 Đường Láng, Hà Nội', 'user'),
(2, 'Tran Thi My Hoa', 'Nu', '0912345678', 'hoa@gmail.com', '0192023a7bbd73250516f069df18b500', '456 Nguyễn Trãi, TP.HCM', 'admin'),
(6, 'Tran My', 'Nam', '0912345679', 'my@gmail.com', 'ffbcd37b685540bdc3e9770d8865526a', '456 Đường Pasteur, TP.HCM', 'user'),
(9, 'Nguyen Ngoc Thao Nguyen', 'Nữ', '0112233446', 'nguyen@gmail.com', '6e97123d7be0c38c111ff6d7d6b274cd', '123 Nguyen Tri Phuong,phương 3,Quan 10,TpHCM', 'user'),
(10, 'Nguyen Thi Tuyet Nhung', 'Nữ', '0763098559', 'nhung@gmail.com', '13df51b83fbeeffd4e555f162fe27c35', 'Le Van Quoi,Binh Tri Dong A,quan Binh Tan,tpHCM', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbl_sanpham`
--

CREATE TABLE `tbl_sanpham` (
  `id_sanpham` int(11) NOT NULL,
  `tensanpham` varchar(100) NOT NULL,
  `giasanpham` decimal(10,2) NOT NULL,
  `soluongsanpham` int(11) DEFAULT 0,
  `mota` text DEFAULT NULL,
  `id_danhmuc` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_sanpham`
--

INSERT INTO `tbl_sanpham` (`id_sanpham`, `tensanpham`, `giasanpham`, `soluongsanpham`, `mota`, `id_danhmuc`, `hinh_anh`) VALUES
(1, 'Labubu Cute Pets Blind Box', 120000.00, 9, 'Hộp bí ẩn Labubu Cute Pets với các nhân vật thú cưng', 4, 'uploads/images/labubu_cute_pets.jpg\r\n'),
(2, 'Hot Wheels Nissan Skyline 2000GT-R', 90000.00, 5, 'Xe mô hình Nissan Skyline 2000GT-R LBWK từ bộ sưu tập Premium Collector Set', 1, 'uploads/images/hotwheels_skyline_2000gtr.jpg'),
(3, 'Mini GT Porsche 911 Turbo', 150000.00, 5, 'Mô hình Porsche 911 Turbo tỷ lệ 1:64, chi tiết cao cấp từ Mini GT', 2, 'uploads/images/minigt_porsche_911.jpg'),
(4, 'TarmaWorks Mazda RX-7', 130000.00, 4, 'Mô hình Mazda RX-7 tỷ lệ 1:64, thiết kế độc đáo từ TarmaWorks', 5, 'uploads/images/tarmaworks_mazda_rx7.jpg'),
(5, 'Cry Baby Blind Box Series 1', 200000.00, 10, 'Hộp bí ẩn Cry Baby với nhân vật ngẫu nhiên, phiên bản Series 1', 3, 'uploads/images/crybaby_series1.jpg'),
(6, 'Labubu The Monsters Blind Box', 220000.00, 7, 'Hộp bí ẩn Labubu từ bộ sưu tập The Monsters, nhân vật dễ thương', 4, 'uploads/images/labubu_monsters.jpg'),
(7, 'Baby Three V.1 Collectible Figure', 180000.00, 5, 'Nhân vật Baby Three phiên bản V.1, thiết kế độc đáo từ POP MART', 6, 'uploads/images/babythree_v1.jpg'),
(8, 'Hot Wheels Chevrolet Camaro Z28', 110000.00, 6, 'Mô hình Chevrolet Camaro Z28 tỷ lệ 1:64, phiên bản giới hạn 2025', 1, 'uploads/images/hotwheels_camaro_z28.jpg'),
(9, 'Hot Wheels Toyota Supra', 95000.00, 7, 'Xe mô hình Toyota Supra từ bộ sưu tập Retro Entertainer', 1, 'uploads/images/hotwheels_toyota_supra.jpg'),
(10, 'Hot Wheels Ford Mustang Mach 1', 115000.00, 5, 'Ford Mustang Mach 1 tỷ lệ 1:64, thiết kế cổ điển', 1, 'uploads/images/hotwheels_mustang_mach1.jpg'),
(11, 'Mini GT Lamborghini Aventador SVJ', 160000.00, 4, 'Mô hình Lamborghini Aventador SVJ tỷ lệ 1:64, chi tiết cao cấp', 2, 'uploads/images/minigt_lamborghini_aventador.jpg'),
(12, 'Mini GT McLaren 720S', 155000.00, 3, 'McLaren 720S tỷ lệ 1:64 từ Mini GT, thiết kế hiện đại', 2, 'uploads/images/minigt_mclaren_720s.jpg'),
(13, 'TarmaWorks Nissan GT-R R35', 140000.00, 5, 'Mô hình Nissan GT-R R35 tỷ lệ 1:64, phiên bản đặc biệt', 5, 'uploads/images/tarmaworks_gtr_r35.jpg'),
(14, 'Labubu Fantasy Series Blind Box', 230000.00, 6, 'Hộp bí ẩn Labubu Fantasy Series với nhân vật ngẫu nhiên', 4, 'uploads/images/labubu_fantasy.jpg'),
(15, 'Baby Three V.2 Collectible Figure', 190000.00, 4, 'Nhân vật Baby Three phiên bản V.2, thiết kế mới từ POP MART', 6, 'uploads/images/babythree_v2.jpg'),
(16, 'Cry Baby Happy Tears Blind Box', 210000.00, 5, 'Hộp bí ẩn Cry Baby Happy Tears, phiên bản vui nhộn', 3, 'uploads/images/crybaby_happytears.jpg');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbl_chitietdonhang`
--
ALTER TABLE `tbl_chitietdonhang`
  ADD PRIMARY KEY (`id_chitietdonhang`),
  ADD KEY `id_donhang` (`id_donhang`),
  ADD KEY `id_sanpham` (`id_sanpham`);

--
-- Chỉ mục cho bảng `tbl_danhmuc`
--
ALTER TABLE `tbl_danhmuc`
  ADD PRIMARY KEY (`id_danhmuc`);

--
-- Chỉ mục cho bảng `tbl_donhang`
--
ALTER TABLE `tbl_donhang`
  ADD PRIMARY KEY (`id_donhang`),
  ADD KEY `id_nguoidung` (`id_nguoidung`);

--
-- Chỉ mục cho bảng `tbl_giohang`
--
ALTER TABLE `tbl_giohang`
  ADD PRIMARY KEY (`id_giohang`),
  ADD KEY `id_nguoidung` (`id_nguoidung`);

--
-- Chỉ mục cho bảng `tbl_nguoidung`
--
ALTER TABLE `tbl_nguoidung`
  ADD PRIMARY KEY (`id_nguoidung`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `tbl_sanpham`
--
ALTER TABLE `tbl_sanpham`
  ADD PRIMARY KEY (`id_sanpham`),
  ADD KEY `id_danhmuc` (`id_danhmuc`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `tbl_chitietdonhang`
--
ALTER TABLE `tbl_chitietdonhang`
  MODIFY `id_chitietdonhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `tbl_danhmuc`
--
ALTER TABLE `tbl_danhmuc`
  MODIFY `id_danhmuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `tbl_donhang`
--
ALTER TABLE `tbl_donhang`
  MODIFY `id_donhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `tbl_giohang`
--
ALTER TABLE `tbl_giohang`
  MODIFY `id_giohang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tbl_nguoidung`
--
ALTER TABLE `tbl_nguoidung`
  MODIFY `id_nguoidung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `tbl_sanpham`
--
ALTER TABLE `tbl_sanpham`
  MODIFY `id_sanpham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `tbl_chitietdonhang`
--
ALTER TABLE `tbl_chitietdonhang`
  ADD CONSTRAINT `tbl_chitietdonhang_ibfk_1` FOREIGN KEY (`id_donhang`) REFERENCES `tbl_donhang` (`id_donhang`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_chitietdonhang_ibfk_2` FOREIGN KEY (`id_sanpham`) REFERENCES `tbl_sanpham` (`id_sanpham`);

--
-- Các ràng buộc cho bảng `tbl_donhang`
--
ALTER TABLE `tbl_donhang`
  ADD CONSTRAINT `tbl_donhang_ibfk_1` FOREIGN KEY (`id_nguoidung`) REFERENCES `tbl_nguoidung` (`id_nguoidung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tbl_giohang`
--
ALTER TABLE `tbl_giohang`
  ADD CONSTRAINT `tbl_giohang_ibfk_1` FOREIGN KEY (`id_nguoidung`) REFERENCES `tbl_nguoidung` (`id_nguoidung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tbl_sanpham`
--
ALTER TABLE `tbl_sanpham`
  ADD CONSTRAINT `tbl_sanpham_ibfk_1` FOREIGN KEY (`id_danhmuc`) REFERENCES `tbl_danhmuc` (`id_danhmuc`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
