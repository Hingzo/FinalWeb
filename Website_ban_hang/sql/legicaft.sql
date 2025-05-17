

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


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
(4, 'Labubu', 10, 'Đồ chơi blind box như Cry Baby, Labubu, Baby Three với nhân vật ngẫu nhiên');

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
(1, '2025-05-15', 230000.00, 'Dang xu ly', 'Giao trong ngày', 1);

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
(1, 'Le Nguyen Mai Quynh', 'Nu', '0901234567', 'quynh@gmail.com', MD5('user123'), '123 Đường Láng, Hà Nội', 'user'),
(2, 'Tran Thi My Hoa', 'Nu', '0912345678', 'hoa@gmail.com', MD5('admin123'), '456 Nguyễn Trãi, TP.HCM', 'admin'),
(6, 'Tran My', 'Nam', '0912345679', 'my@gmail.com', MD5('my123'), '456 Đường Pasteur, TP.HCM', 'user');

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
(1, 'Hot Wheel 1\r\n', 120000.00, 8, 'Mô hình xe Ferrari SF90 Stradale tỷ lệ 1:64, phiên bản 2025 từ Hot Wheels', 1, 'hotwheels_ferrari_sf90.jpg'),
(2, 'Hot Wheels Nissan Skyline 2000GT-R', 90000.00, 5, 'Xe mô hình Nissan Skyline 2000GT-R LBWK từ bộ sưu tập Premium Collector Set', 1, 'uploads/images/hotwheels_skyline_2000gtr.jpg'),
(3, 'Mini GT Porsche 911 Turbo', 150000.00, 6, 'Mô hình Porsche 911 Turbo tỷ lệ 1:64, chi tiết cao cấp từ Mini GT', 1, 'uploads/images/minigt_porsche_911.jpg'),
(4, 'TarmaWorks Mazda RX-7', 130000.00, 4, 'Mô hình Mazda RX-7 tỷ lệ 1:64, thiết kế độc đáo từ TarmaWorks', 1, 'uploads/images/tarmaworks_mazda_rx7.jpg'),
(5, 'Cry Baby Blind Box Series 1', 200000.00, 10, 'Hộp bí ẩn Cry Baby với nhân vật ngẫu nhiên, phiên bản Series 1', 2, 'uploads/images/crybaby_series1.jpg'),
(6, 'Labubu The Monsters Blind Box', 220000.00, 7, 'Hộp bí ẩn Labubu từ bộ sưu tập The Monsters, nhân vật dễ thương', 2, 'uploads/images/labubu_monsters.jpg'),
(7, 'Baby Three V.1 Collectible Figure', 180000.00, 5, 'Nhân vật Baby Three phiên bản V.1, thiết kế độc đáo từ POP MART', 2, 'uploads/images/babythree_v1.jpg');

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
  MODIFY `id_chitietdonhang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tbl_danhmuc`
--
ALTER TABLE `tbl_danhmuc`
  MODIFY `id_danhmuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `tbl_donhang`
--
ALTER TABLE `tbl_donhang`
  MODIFY `id_donhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tbl_giohang`
--
ALTER TABLE `tbl_giohang`
  MODIFY `id_giohang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tbl_nguoidung`
--
ALTER TABLE `tbl_nguoidung`
  MODIFY `id_nguoidung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `tbl_sanpham`
--
ALTER TABLE `tbl_sanpham`
  MODIFY `id_sanpham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
