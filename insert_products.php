<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "smartsolutions");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Product data
$products = [
    ["id" => 1, "name" => "Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W", "price" => 25195.00, "image" => "image/desktop1.png"],
    ["id" => 2, "name" => "Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W", "price" => 14795.00, "image" => "image/desktop2.png"],
    ["id" => 3, "name" => "MSI Thin A15 B7UCX-084PH 15.6 / FHD 144Hz AMD RYZEN 5 7535HS/8GB/512GBSSD/RTX 2050 4GB/WIN11 Laptop", "price" => 38995.00, "image" => "image/laptop1.png"],
    ["id" => 4, "name" => "Lenovo V15 G4 IRU 15.6 / FHD Intel Core i5- 1335U/8GB DDR4/512GB M.2 SSD Laptop MN", "price" => 29495.00, "image" => "image/laptop2.png"],
    ["id" => 5, "name" => "Team Elite Vulcan TUF 16gb 2x8 3200mhz Ddr4 Gaming Memory", "price" => 1999.00, "image" => "image/deal1.png"],
    ["id" => 6, "name" => "Team Elite Plus 8gb 1x8 3200Mhz Black Gold Ddr4 Memory", "price" => 1045.00, "image" => "image/deal2.png"],
    ["id" => 7, "name" => "G.Skill Ripjaws V 16gb 2x8 3200mhz Ddr4 Memory Black", "price" => 2185.00, "image" => "image/deal3.png"],
    ["id" => 8, "name" => "Team Elite 8gb 1x8 1600mhz Ddr3 with Heatspreader Memory", "price" => 1065.00, "image" => "image/deal4.png"],
    ["id" => 9, "name" => "AMD Ryzen 5 Pro 4650G Socket Am4 3.7ghz with Radeon Vega 7 Processor with Wraith Stealth Cooler MPK", "price" => 5845.00, "image" => "image/deal5.png"],
    ["id" => 10, "name" => "Team Elite TForce Delta TUF 16GB 2x8 3200mhz Ddr4 RGB Gaming Memory", "price" => 3155.00, "image" => "image/deal6.png"],
    ["id" => 11, "name" => "AMD Ryzen 5 5600G Socket Am4 3.9GHz with Radeon Vega 7 Processor Wraith Stealth Cooler", "price" => 6895.00, "image" => "image/ryzen5600g.png"],
    ["id" => 12, "name" => "AMD Ryzen 5 5600X Socket AM4 3.7GHz with Wraith Stealth Cooler VR Ready Premium Desktop Processor", "price" => 6395.00, "image" => "image/ryzen5600x.png"],
    ["id" => 13, "name" => "AMD Ryzen 7 5700X Socket AM4 3.4GHz Processor with AMD Wraith Stealth cooler MPK", "price" => 9975.00, "image" => "image/ryzen5700x.png"],
    ["id" => 14, "name" => "AMD Ryzen 5 5600 Socket Am4 3.5GHz Processor with Wraith stealth cooler MPK Processor", "price" => 6300.00, "image" => "image/ryzen5600.png"],
    ["id" => 15, "name" => "AMD Ryzen 5 2400g Socket Am4 3.6ghz with Radeon RX Vega 11 Processor MPK", "price" => 3762.00, "image" => "image/ryzen2400g.png"],
    ["id" => 16, "name" => "AMD Ryzen 3 3200g Socket Am4 3.6ghz with Radeon Vega 8 Processor", "price" => 4050.00, "image" => "image/ryzen3200g.png"],
    ["id" => 17, "name" => "AMD Ryzen 5 Pro 4650G Socket Am4 3.7ghz with Radeon Vega 7 Processor with Wraith Stealth Cooler MPK", "price" => 8595.00, "image" => "image/ryzen52400g.png"],
    ["id" => 18, "name" => "Intel Core i3-12100 Alder Lake Socket 1700 4.30GHz Processor MPK", "price" => 6250.00, "image" => "image/IntelCorei3-12100.png"],
    ["id" => 19, "name" => "Intel Core I5-12400 Alder Lake Socket 1700 2.5GHz Processor MPK", "price" => 8350.00, "image" => "image/IntelCorei5-12400.png"],
    ["id" => 20, "name" => "Intel Core i5-14600K Raptor Lake Socket LGA 1700 2.50GHz Processor", "price" => 25995.00, "image" => "image/IntelCorei5-14600K.png"],
    ["id" => 21, "name" => "Intel Core I5-11400 Socket 1200 2.60GHz Intel UHD Graphics 730 Ttp Rocket Lake Processor", "price" => 10395.00, "image" => "image/IntelCorei5-11400.png"],
    ["id" => 22, "name" => "AMD Ryzen 7 9700X 3.8GHz AM5 Socket DDR5 Processor", "price" => 25395.00, "image" => "image/ryzen9700x.png"],
    ["id" => 23, "name" => "MSI A520m-A Pro AMD Am4 Ddr4 Micro-ATX PCB Gaming Motherboard", "price" => 3899.00, "image" => "image/MSI_A520m.png"],
    ["id" => 24, "name" => "Asrock B550M Pro4 Socket Am4 Ddr4 Motherboard", "price" => 6540.00, "image" => "image/Asrock_B550M.png"],
    ["id" => 25, "name" => "Asrock B450M Steel Legend Am4 Gaming Motherboard", "price" => 5950.00, "image" => "image/Asrock_B450M.png"],
    ["id" => 26, "name" => "Asus Prime A520M-K Socket AM4 Ddr4Gaming Motherboard", "price" => 4094.00, "image" => "image/Asus_Prime_A520M.png"],
    ["id" => 27, "name" => "MSI PRO H610M-E Socket LGA 1700 Ddr4 Lightning Gen 4 PCI-E User Friendly Design Gaming Motherboard", "price" => 5209.00, "image" => "image/MSI_PRO_H610M.png"],
    ["id" => 28, "name" => "Asus Prime B550M-A Wifi II Socket Am4 Ddr4 Gaming Motherboard", "price" => 6850.00, "image" => "image/Asus_Prime_B550M.png"],
    ["id" => 29, "name" => "Biostar A520MHP Socket Am4 DDR4 Motherboard", "price" => 3000.00, "image" => "image/Biostar_A520MHP.png"],
    ["id" => 30, "name" => "Asrock B550M Pro SE Socket Am4 Ddr4 Motherboard", "price" => 5750.00, "image" => "image/Asrock_B550M_Pro.png"],
    ["id" => 31, "name" => "MSI Mag B550m Pro-Vdh WIFI mATX AM4 Ddr4 Gaming Motherboard", "price" => 6835.00, "image" => "image/MSI_Mag_B550m.png"],
    ["id" => 32, "name" => "Asrock X570S Phantom Gaming Riptide Socket Am4 Ddr4 Motherboard", "price" => 10150.00, "image" => "image/Asrock_X570S.png"],
    ["id" => 33, "name" => "Asus ROG Strix B550-F Gaming Wifi II Socket Am4 Ddr4 Aura Sync RGB Lighting Best Gaming Audio Gaming Motherboard", "price" => 12850.00, "image" => "image/AsusROGStrixB550.png"],
    ["id" => 34, "name" => "MSI PRO X670-P WIFI ATX DDR5 AM5 2.5G LAN with Wi-Fi 6E Solution Gaming Motherboard", "price" => 19995.00, "image" => "image/MSIPROX670-PWIFIATX.png"],
    ["id" => 35, "name" => "MSI NVIDIA® GeForce RTX 3060 Ventus 2X OC 12gb 192bit GDdr6 Gaming Videocard LHR", "price" => 31595.00, "image" => "image/MSI_RTX_3060_Ventus.png"],
    ["id" => 36, "name" => "Asrock RX 6600 8G CHALLENGER D 8gb 128bit GDdr6 Dual Fan Gaming Videocard", "price" => 13100.00, "image" => "image/Asrock_RX_6600_8G.png"],
    ["id" => 37, "name" => "ASUS Dual Radeon RX 6600 DUAL-RX6600-8G-V3 8GB 128-bit GDDR6 Videocard", "price" => 14295.00, "image" => "image/ASUS_Dual_RX_6600.png"],
    ["id" => 38, "name" => "Galax RTX 4060 8GB 1-Click OC 2X V2 Dual Fan (46NSL8MD9NXV) 128-bit GDDR6 Videocard", "price" => 18995.00, "image" => "image/Galax_RTX_4060_8GB.png"],
    ["id" => 39, "name" => "MSI NVIDIA® GeForce GTX 1650 D6 Ventus XS OC/XC OC V3 4gb 128bit GDdr6 Gaming Videocard", "price" => 12430.00, "image" => "image/msi1650.png"],
    ["id" => 40, "name" => "Gigabyte NVIDIA® GeForce RTX 3060 Gaming OC LHR R2.0 192bit GDdr6 Gaming Videocard RGB", "price" => 33055.00, "image" => "image/GigabyteRtx3060Gaming.png"],
    ["id" => 41, "name" => "Gigabyte Rx 6600 Eagle GV-R66EAGLE-8GD 8gb 128bit GDdr6, WINDFORCE 3X Cooling System,Integrated w/ 8GB GDDR6 128-bit memory interface Gaming Videocard", "price" => 24999.00, "image" => "image/Gigabyte-Rx6600-Eagle.png"],
    ["id" => 42, "name" => "Gigabyte NVIDIA® GeForce RTX™ 4070 TI Super Gaming OC 16GB 256-Bit GDDR6X Videocard", "price" => 60260.00, "image" => "image/Gigabyte_RTX_4070.png"],
    ["id" => 43, "name" => "Galax NVIDIA® GeForce RTX 4070 EX-Gamer PINK 12GB 192 BIT GDDR6X 47NOM7MD7KWH Videocard", "price" => 39216.00, "image" => "image/Galax_GeForce_RTX_4070.png"],
    ["id" => 44, "name" => "Asus ROG Strix Rtx 4060 Ti ROG-STRIX-RTX4060TI-O8G-GAMING 8gb 128bit GDdr6 Gaming Videocard", "price" => 32095.00, "image" => "image/AsusROGStrixRtx4060.png"],
    ["id" => 45, "name" => "ASUS Nvidia GeForce TUF Gaming RTX 4070 Ti White OC Edition 12GB 192bit GDDR6X Videocardd", "price" => 51950.00, "image" => "image/ASUS_Nvidia_GeForce_TUF_Gaming_RTX_4070.png"],
    ["id" => 46, "name" => "ASUS Nvidia GeForce RTX 4070 Ti OC Edition (PROART-RTX4070-O12G) 12GB 192bit GDDR6X Gaming Videocard", "price" => 48675.00, "image" => "image/ASUSNvidiaGeForceRTX4070.png"],
    ["id" => 47, "name" => "Team Elite Vulcan TUF 16gb 2x8 3200mhz Ddr4 Gaming Memory", "price" => 1999.00, "image" => "image/deal1.png"],
    ["id" => 48, "name" => "Team Elite Plus 8gb 1x8 3200Mhz Black Gold Ddr4 Memory", "price" => 1045.00, "image" => "image/deal2.png"],
    ["id" => 49, "name" => "G.Skill Ripjaws V 16gb 2x8 3200mhz Ddr4 Memory Black", "price" => 2185.00, "image" => "image/deal3.png"],
    ["id" => 50, "name" => "Team Elite TForce Delta 16gb 2x8 3200mhz Ddr4 RGB Memory White", "price" => 4454.00, "image" => "image/Team_Elite_TForce_Delta_16gb.png"],
    ["id" => 51, "name" => "Team Elite TForce Delta 2x8 3200mhz Ddr4 Memory", "price" => 4553.00, "image" => "image/tforce-delta.png"],
    ["id" => 52, "name" => "Team Elite Plus 8gb 1x8 3200Mhz Ddr4 Memory Black Red", "price" => 1719.00, "image" => "image/TPRD48G3200HC2201.png"],
    ["id" => 53, "name" => "G.Skill Trident Z Neo 16gb 2x8 3600mhz Ddr4 RGB Memory", "price" => 5581.00, "image" => "image/gskill-trident-z-rgb-neo.png"],
    ["id" => 54, "name" => "Kingston Fury Beast KF436C18BB2A/16 16gb 1x16 3600MT/s Ddr4 Memory RGB Black", "price" => 3068.00, "image" => "image/kingstonmemory.png"],
    ["id" => 55, "name" => "Adata XPG Spectrix D50 16GB 2x8 3200mHz DDR4 RGB White Memory", "price" => 3500.00, "image" => "image/xpg.png"],
    ["id" => 56, "name" => "Team Elite TForce Delta TUF 16GB 2x8 3200mhz Ddr4 RGB Gaming Memory", "price" => 3155.00, "image" => "image/deal6.png"],
    ["id" => 57, "name" => "G.Skill Trident Z5 Neo 32gb 2x16 6000mhz Ddr5 RGB AMD Expo Memory", "price" => 8655.00, "image" => "image/g.skill.png"],
    ["id" => 58, "name" => "Corsair Vengeance Pro16gb 2x8gb Ddr4 3000MHz XMP 2.0 Aluminum Heat Spreader RGB LED Lighting White Gaming Memory", "price" => 4499.00, "image" => "image/corsairvengeancememory.png"],
    ["id" => 59, "name" => "Kingston NV1 PCIe M.2 3.0 NVME 500GB, 250GB, 1TB, 2TB and NV2 PCIe 4.0 250GB, 500GB, 1TB, 2TB", "price" => 2835.00, "image" => "image/Kingston_NV1_500GB.png"],
    ["id" => 60, "name" => "Team Group GX2 512GB Sata III 2.5 Solid State Drive", "price" => 3295.00, "image" => "image/Team_Group_GX2_512GB.png"],
    ["id" => 61, "name" => "Team Group MP33 256GB M.2 PCIe NVME Solid State Drive", "price" => 1425.00, "image" => "image/Team_Group_MP33_256GB.png"],
    ["id" => 62, "name" => "Crucial P3 Plus 500GB, 1TB PCIe 4.0 Gen4 NVMe M.2 Storage Spacious Solid State Drive", "price" => 3075.00, "image" => "image/Crucial_P3_Plus_500GB_PCIe.png"],
    ["id" => 63, "name" => "Team Group GX2 256gb SATA 2.5 Faster Booting & File Transferring, Windows 10 Compatible ,Light weight Solid State Drive", "price" => 1521.00, "image" => "image/Team_Group_GX2_256gb.png"],
    ["id" => 64, "name" => "Lexar NM610 Pro 1TB M.2 NVME Solid State Drive", "price" => 3525.00, "image" => "image/Lexar_NM610_Pro_1TB.png"],
    ["id" => 65, "name" => "Adata SU650 Solid State Drive 512GB SATA 2.5", "price" => 2095.00, "image" => "image/Adata_SU650_Solid_State_Drive_512GB_SATA.png"],
    ["id" => 66, "name" => "MSI Spatium M450 500GB PCIE NVME M.2 Solid State Drive", "price" => 3765.00, "image" => "image/MSI_Spatium_M450_500GB_PCIE.png"],
    ["id" => 67, "name" => "Apacer Panther AS350 256GB/512GB 2.5 SATA3 Optimized Durability & Excellent Performance at high Stability Solid State Drive", "price" => 1495.00, "image" => "image/Apacer_Panther.png"],
    ["id" => 68, "name" => "Samsung 970 EVO Plus 1TB NVME M.2 Solid State Drive", "price" => 7555.00, "image" => "image/Samsung_970_EVO_Plus_1TB.png"],
    ["id" => 69, "name" => "Gigabyte GP-GSTFS31100TNTD 1TB SATA 2.5 Solid State Drive", "price" => 4345.00, "image" => "image/Gigabyte_GP.png"],
    ["id" => 70, "name" => "Samsung 990 Pro 2TB NVME M.2 Solid State Drive", "price" => 11750.00, "image" => "image/Samsung_990_Pro_2TB.png"],
    ["id" => 71, "name" => "Acer AC-550 550w Full Modular 80plus Bronze Power Supply", "price" => 1415.00, "image" => "image/Acer_AC-550.png"],
    ["id" => 72, "name" => "Corsair CX650 650 watts 80 Plus Bronze Power Supply", "price" => 3705.00, "image" => "image/Corsair_CX650_650.png"],
    ["id" => 73, "name" => "Seasonic Focus Gold 750W, 50W 80 Plus Multi-GPU setup Semi-Modular Cables Power Supply", "price" => 4875.00, "image" => "image/SeasonicFocus.png"],
    ["id" => 74, "name" => "MSI MAG A650BN 650Watts / A550BN 550Watts 80+ Non Modular Power Supply Bronze", "price" => 3385.00, "image" => "image/MSI_MAG_A650BN.png"],
    ["id" => 75, "name" => "Gigabyte P450B 450 watts 80 Plus Bronze Power Supply", "price" => 2385.00, "image" => "image/Gigabyte_P450B_450.png"],
    ["id" => 76, "name" => "Coolermaster MWE850 V2 MPE-8501-AFAAG-TW 850watts Fully Modular 80+ Gold Power Supply", "price" => 7025.00, "image" => "image/CoolermasterMWE850V2.png"],
    ["id" => 77, "name" => "Ramsta RG1000 1000W 80+ Gold Fully Modular Power Supply", "price" => 7450.00, "image" => "image/RamstaRG10001000W.png"],
    ["id" => 78, "name" => "MSI MAG A850GL 850Watts PCIE5 80+ Full Modular Power Supply Gold", "price" => 7145.00, "image" => "image/MSI_MAG_A850GL.png"],
    ["id" => 79, "name" => "Seasonic Focus Plus 650W Fully Modular Power Supply Platinum", "price" => 7495.00, "image" => "image/Seasonic_Focus_Plus_650W.png"],
    ["id" => 80, "name" => "Seasonic Prime 1300W Fully Modular Power Supply Gold", "price" => 13250.00, "image" => "image/seasonicprime.png"],
    ["id" => 81, "name" => "SuperFlower Leadex III 550W 80+ Fully Modular Power Supply RGB Gold", "price" => 5895.00, "image" => "image/superflower.png"],
    ["id" => 82, "name" => "Asus ROG Thor Power Supply 1200w RGB", "price" => 23435.00, "image" => "image/asuspowersupply.png"],
    ["id" => 83, "name" => "InPlay Meteor 03 Mid Tower Black and White Tempered Glass Gaming Case", "price" => 1095.00, "image" => "image/InPlay_Meteor_03.png"],
    ["id" => 84, "name" => "RAKK MIRAD Matx Black and White Tempered Glass Gaming PC Case", "price" => 1295.00, "image" => "image/RAKK_MIRAD_Matx.png"],
    ["id" => 85, "name" => "RAKK MASID MATX Tempered Gaming Case Black", "price" => 1050.00, "image" => "image/RAKK_MASID_MATX.png"],
    ["id" => 86, "name" => "DarkFlash DLM21 Mesh Mid Tower Black, White and Pink White PC Case", "price" => 2255.00, "image" => "image/DarkFlash.png"],
    ["id" => 87, "name" => "MSI MAG Forge 100R Mid Tower PC Case Black Support ATX / Micro ATX / Mini-ITX, Tempered Glass", "price" => 3635.00, "image" => "image/MSI_MAG_Forge.png"],
    ["id" => 88, "name" => "Fantech Pulse CG71 RGB Mid Tower Case White", "price" => 1595.00, "image" => "image/Fantech_Pulse.png"],
    ["id" => 89, "name" => "DarkFlash C285P ATX Tempered Glass Side Panel Gaming PC Case Black", "price" => 2915.00, "image" => "image/DarkFlashC285PBlack.png"],
    ["id" => 90, "name" => "LianLi LanCool 215-X ATX Mid Tower Honeycomb Vent Multi Cooling System Support Gaming PC Case White", "price" => 5175.00, "image" => "image/lianli.png"],
    ["id" => 91, "name" => "DarkFlash C285P ATX Tempered Glass Side Panel Gaming PC Case White", "price" => 3095.00, "image" => "image/DarkFlashC285PWhite.png"],
    ["id" => 92, "name" => "NZXT H9 Flow Mid Tower ATX Gaming PC Case White", "price" => 9405.00, "image" => "image/NZXT_H9_Flow.png"],
    ["id" => 93, "name" => "Keytech Cyborg ROG Micro ATX with 6fans Gaming PC Case Black", "price" => 2495.00, "image" => "image/Keytech_Cyborg_ROG_Black.png"],
    ["id" => 94, "name" => "MSI MAG PANO M100R Project Zero Micro ATX PC Case White", "price" => 5535.00, "image" => "image/MSI_MAG_PANO.png"],
    ["id" => 95, "name" => "Lenovo V15 G4 IRU 15.6 FHD Intel Core i5- 1335U/8GB DDR4/512GB M.2 SSD Laptop MN", "price" => 27995.00, "image" => "image/ideapad.png"],
    ["id" => 96, "name" => "MSI Cyborg 15 A13VF-433PH 15.6 Raptor Lake i7-13620H Laptop", "price" => 86995.00, "image" => "image/msicyborg.png"],
    ["id" => 97, "name" => "Acer Aspire 3 15.6 Intel Core i5-1235U/4GB+4GB/256GB SSD/Win11 Laptop Silver PS OBP", "price" => 28255.00, "image" => "image/aceraspire.png"],
    ["id" => 98, "name" => "Lenovo ThinkPad E15 Gen4 15.6 Intel Core i5-1235u/16GB DDR4 3200/512GB SSD PCIe/Win11 Pro MN", "price" => 30635.00, "image" => "image/thinkpad.png"],
    ["id" => 99, "name" => "Lenovo Tab P11 Gen 1 11.0 2K Qualcomm Snapdragon 662 6GB/128GB Wi-Fi Tablet Storm Gray", "price" => 15950.00, "image" => "image/lenovotab.png"],
    ["id" => 100, "name" => "Gigabyte G6X 9KG-43PH854SH 16 FHD+ 165Hz/i7-13650HX/16GB DDR5/1TB SSD/RTX4060 8GD6/Win11 Laptop", "price" => 69995.00, "image" => "image/Gigabyte_G6X.png"],
    ["id" => 101, "name" => "MSI Cyborg 15 A13VF-1256PH 15.6 FHD IPS i5-13420H/8GB DDR5/512GB NVMe SSD/RTX4060 GDDR6/Win11 Laptop", "price" => 51175.00, "image" => "image/msicyborg2.png"],
    ["id" => 102, "name" => "MSI Thin A15 B7UCX-084PH 15.6 FHD 144Hz AMD RYZEN 5 7535HS/8GB/512GBSSD/RTX 2050 4GB/WIN11 Laptop", "price" => 38995.00, "image" => "image/msithin.png"],
    ["id" => 103, "name" => "Nvision EG24S1 PRO 180HZ Flat IPS Panel 24 Gaming Monitor Black", "price" => 5250.00, "image" => "image/nvision1.png"],
    ["id" => 104, "name" => "Nvision N2455PRO-B 100Hz IPS Panel 23.8 Monitor Black", "price" => 4000.00, "image" => "image/nvision2.png"],
    ["id" => 105, "name" => "ViewPlus MG-27KI 27 165Hz 1MS 2K IPS Gaming Monitor Black", "price" => 7420.00, "image" => "image/ViewPlus.png"],
    ["id" => 106, "name" => "Gamdias Atlas HD24CII 24 180HZ FHD Curved Monitor", "price" => 6000.00, "image" => "image/Gamdias.png"],
    ["id" => 107, "name" => "MSI PRO MP251 24.5 FHD 100HZ IPS Monitor", "price" => 5520.00, "image" => "image/MSI_PRO_MP251.png"],
    ["id" => 108, "name" => "Viewplus MX-22 / ML-22 21.5 75Hz VA Monitor", "price" => 4485.00, "image" => "image/Viewplus1.png"],
    ["id" => 109, "name" => "YGT TN24FHD-GD 23.8 FHD LED Monitor", "price" => 2190.00, "image" => "image/YGT_TN24FHD-GD.png"],
    ["id" => 110, "name" => "HP Series 5 524SF 23.8 100HZ FHD IPS Monitor", "price" => 6000.00, "image" => "image/HP_Series_5.png"],
    ["id" => 111, "name" => "Viewplus MX-24CH 23.6? 165Hz Curved Monitor", "price" => 5290.00, "image" => "image/Viewplus_MX-24CH.png"],
    ["id" => 112, "name" => "Gigabyte G24F and G24F-2-TW 23.8 SS IPS 165Hz Gaming Monitor", "price" => 8900.00, "image" => "image/Gigabyte_G24F.png"],
    ["id" => 113, "name" => "MSI Optix G241V E2 23.8 75Hz 1ms IPS Freesync Monitor", "price" => 7995.00, "image" => "image/MSI_Optix_G241V.png"],
    ["id" => 114, "name" => "AOC 24G2SPE 24 165Hz IPS Gaming Monitor Black/Red", "price" => 9040.00, "image" => "image/AOC_24G2SPE.png"],
    ["id" => 115, "name" => "RAKK TALA 81 Keys White / Trimode / RGB / Universal Hotswap / Gasket Mount / MS Red Switch / Black", "price" => 1995.00, "image" => "image/RAKK_TALA_81.png"],
    ["id" => 116, "name" => "RAKK Hanan Ultra|Gasket Mount Aluminum CNC Case|81 Keys Trimode Mechanical Keyboard|White", "price" => 3495.00, "image" => "image/RAKK_Hanan_Ultra_Gasket.png"],
    ["id" => 117, "name" => "Fantech MK890 V2 Atom 96 Hot swappable 3pin Red switch Mechanical Keyboard Sky Blue", "price" => 1220.00, "image" => "image/Fantech_MK890_V2.png"],
    ["id" => 118, "name" => "RAKK PLUMA V2 67 Keys Trimode Outemu Red Switch Mechanical Gaming Keyboard RGB Black", "price" => 1895.00, "image" => "image/RAKK_PLUMA_V2_67_Keys.png"],
    ["id" => 119, "name" => "Inplay NK680-B Red switch Mechanical Gaming Keyboard Black", "price" => 630.00, "image" => "image/Inplay_NK680-B_Red_switch.png"],
    ["id" => 120, "name" => "Royal Kludge RK61 Trimode Red switch Mechanical Keyboard Black", "price" => 2200.00, "image" => "image/Royal_Kludge_RK61_Trimode_Red_switch.png"],
    ["id" => 121, "name" => "Aula F99 RGB Gaming Mechanical Hotswappable Keyboard Purple", "price" => 3240.00, "image" => "image/AulaF99RGBGamingMechanicalHotswappable.png"],
    ["id" => 122, "name" => "RAKK HARIBON Ergonomic Mechanical Gaming Keyboard", "price" => 7995.00, "image" => "image/RAKK_HARIBON_Ergonomic_Mechanical.png"],
    ["id" => 123, "name" => "Asus ROG Strix Flare II NX Blue Gaming Keyboard", "price" => 7955.00, "image" => "image/Asus_ROG_Strix_Flare_II_NX_Blue.png"],
    ["id" => 124, "name" => "Redragon K649 PRO Gasket Mechanical Hotswap Tri-mode keyboard Crystal Black", "price" => 3240.00, "image" => "image/Redragon_K649_PRO_Gasket_Mechanical.png"],
    ["id" => 125, "name" => "ONIKUMA G30 Wired Mechanical 84 Key RGB Backlit Keyboard White", "price" => 1670.00, "image" => "image/ONIKUMA_G30_Wired_Mechanical.png"],
    ["id" => 126, "name" => "Royal Kludge RK-M75 Trimode Mechanical Keyboard Oled Screen Phantom", "price" => 3460.00, "image" => "image/Royal_Kludge_RK-M75_Trimode_Mechanical_Keyboard.png"],
    ["id" => 127, "name" => "Logitech G102 Light Sync Black and White Gaming Mouse", "price" => 970.00, "image" => "image/logitech.png"],
    ["id" => 128, "name" => "RAKK DASIG X Ambidextrous Hotswap Trimode PMW3325 Huano 80M RGB Gaming Mouse White", "price" => 1000.00, "image" => "image/rakkmouse.png"],
    ["id" => 129, "name" => "Redragon M916 Lite King Wireless Gaming Mouse White", "price" => 920.00, "image" => "image/reddragonmouse.png"],
    ["id" => 130, "name" => "Redragon M806 Bullseye Gaming Mouse Black", "price" => 860.00, "image" => "image/m806.png"],
    ["id" => 131, "name" => "SteelSeries Rival 3 62513 Gaming Mouse Black", "price" => 1875.00, "image" => "image/steelseries_rival.png"],
    ["id" => 132, "name" => "Razer Basilisk Gaming Mouse", "price" => 3550.00, "image" => "image/RAZER-BASILIK.png"],
    ["id" => 133, "name" => "Asus ROG Pugio RGB Gaming Mouse", "price" => 3195.00, "image" => "image/asusmouse.png"],
    ["id" => 134, "name" => "Corsair Glaive CSCH9302011AP RGB Gaming Mouse", "price" => 2337.00, "image" => "image/corsair-glaive.png"],
    ["id" => 135, "name" => "Asus P502 ROG Gladius II RGB Gaming Mouse", "price" => 4700.00, "image" => "image/rog-gladius.png"],
    ["id" => 136, "name" => "Razer Mamba Elite Gaming Mouse", "price" => 4595.00, "image" => "image/razermouse.png"],
    ["id" => 137, "name" => "Logitech G502 Hero Gaming Mouse", "price" => 2215.00, "image" => "image/g502.png"],
    ["id" => 138, "name" => "Razer Deathadder V2 Chroma Gaming Mouse", "price" => 4299.00, "image" => "image/razermouse1.png"],
    ["id" => 139, "name" => "RAKK LIMAYA+ Trimode RGB Gaming Wireless Headset Black", "price" => 1895.00, "image" => "image/RAKK_LIMAYA_Trimode.png"],
    ["id" => 140, "name" => "Asus TUF Gaming H3 7.1 Gaming Headset Gun Metal / Red / Silver", "price" => 2050.00, "image" => "image/asus_tuf_gaming.png"],
    ["id" => 141, "name" => "ONIKUMA X32 Wired Gaming Headset RGB Black", "price" => 680.00, "image" => "image/ONIKUMA_X32_Wired.png"],
    ["id" => 142, "name" => "Fantech WHG03P Studio Pro 7.1 Surround Sound Headset Grey", "price" => 2015.00, "image" => "image/fantech.png"],
    ["id" => 143, "name" => "Logitech G435 Lightspeed Wireless Gaming Headset Black", "price" => 3100.00, "image" => "image/logitech_g435_lightspeed.png"],
    ["id" => 144, "name" => "SteelSeries HS Arctis Nova 1 AirWeave Memory Foam Noise-Cancelling Mic Black and White Headset", "price" => 3550.00, "image" => "image/steelseries.png"],
    ["id" => 145, "name" => "Kingston HyperX Cloud II Gaming Headset Red", "price" => 5095.00, "image" => "image/hyperx.png"],
    ["id" => 146, "name" => "Razer Electra V2 Gaming Headset", "price" => 3050.00, "image" => "image/razerheadset.png"],
    ["id" => 147, "name" => "Corsair Void Pro CSCA9011150AP RGB 7.1 Wireless Premium Gaming Headset Yellow", "price" => 6595.00, "image" => "image/corsairvoid.png"],
    ["id" => 148, "name" => "Corsair Void Pro CSCA9011156AP 7.1 Premium Gaming Carbon Headset Black", "price" => 3825.00, "image" => "image/corsairheadset.png"],
    ["id" => 149, "name" => "Asus ROG Centurion 7.1 Gaming Headset", "price" => 12995.00, "image" => "image/asusheadset1.png"],
    ["id" => 150, "name" => "Asus ROG Strix Fusion 300 7.1 Gaming Headset", "price" => 9295.00, "image" => "image/asusheadset2.png"],
    ["id" => 151, "name" => "Core i7 12700 / H610 / 16GB DDR4 / 500GB SSD / 550W Power Supply / PC Case M-ATX", "price" => 25195.00, "image" => "image/Core_i5.png"],
    ["id" => 152, "name" => "Ryzen 7 5700G / B450M / 16GB DDR4 / 512GB SSD / 550W Power Supply / PC Case M-ATX", "price" => 21250.00, "image" => "image/Ryzen_7.jpg"],
    ["id" => 153, "name" => "Stratus Intel i5 12th gen | MSI H610 | Kingston 8gb Memory | 500gb | 700w", "price" => 20950.00, "image" => "image/i5.png"],
    ["id" => 154, "name" => "Cirrus AMD Ryzen 7 5700x | MSI B550 | Zotac Rtx-4060 | Team 16gb Memory | Kingston NVMe 2TB", "price" => 14795.00, "image" => "image/ryzn7.png"],
    ["id" => 155, "name" => "Cirrostratus AMD Ryzen 5 5600G | Gigabyte B550 | Gigabyte Rx-6600 | Team 16gb Memory", "price" => 25195.00, "image" => "image/ryzn5.png"],
    ["id" => 156, "name" => "Cumulus AMD Ryzen 5 Pro 4650G | Asrock A320 | Team Elite Plus 8gb Memory | Team 240gb", "price" => 21250.00, "image" => "image/ryzen5.png"],
    ["id" => 157, "name" => "MSI NVIDIA® GeForce GTX 1650 D6 Ventus XS OC/XC OC V3 4gb 128bit GDdr6 Gaming Videocard", "price" => 8550.00, "image" => "image/msi1650.png"],
    ["id" => 158, "name" => "Gigabyte NVIDIA® GeForce RTX 3060 Gaming OC LHR R2.0 192bit GDdr6 Gaming Videocard RGB", "price" => 20250.00, "image" => "image/Gigabyte-RTX-3060-Windforce.png"],
    ["id" => 159, "name" => "COOLERMASTER ML240L ARGB V2 Liquid Cooler WHITE ED (MLW-D24M-A18PW-RW)", "price" => 3350.00, "image" => "image/coolermaster1.png"],
    ["id" => 160, "name" => "Edifier W800BT Plus Black, Red & White Built-in microphone 8.0 noise cancellation Bluetooth v5.1 Stereo Headphones 19GLO", "price" => 1345.00, "image" => "image/edifier.png"],
    ["id" => 161, "name" => "Kingston KVR32S22S8/16 16gb 1x16 3200mhz Low-power auto self-refresh Ddr4 Sodimm Memory", "price" => 3350.00, "image" => "image/kingston.png"],
    ["id" => 162, "name" => "AMD Ryzen 7 5700G Socket Am4 3.8GHz with Radeon Vega 8 Processor", "price" => 11195.00, "image" => "image/ryzen7.png"],
];

// Clear existing products (optional - comment out if you want to keep existing data)
$conn->query("DELETE FROM products");

// Insert products
$success_count = 0;
$error_count = 0;
$errors = [];

foreach ($products as $product) {
    $id = $product['id'];
    $name = $conn->real_escape_string($product['name']);
    $price = $product['price'];
    $image = $conn->real_escape_string($product['image']);
    
    $sql = "INSERT INTO products (id, name, price, image, stock) VALUES ($id, '$name', $price, '$image', 10)";
    
    if ($conn->query($sql) === TRUE) {
        $success_count++;
    } else {
        $error_count++;
        $errors[] = "Error on product ID $id: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Products - Result</title>
    <link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }
        
        .summary {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        
        .summary p {
            margin: 10px 0;
            font-size: 16px;
        }
        
        .back-btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            margin-top: 20px;
            width: 100%;
            box-sizing: border-box;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .back-btn:hover {
            background: #5568d3;
        }
        
        .error-list {
            max-height: 300px;
            overflow-y: auto;
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 12px;
        }
        
        .error-list p {
            margin: 5px 0;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Product Import Result</h1>
        
        <div class="summary">
            <p><strong>Total Products:</strong> <?php echo count($products); ?></p>
            <p><strong>Successfully Inserted:</strong> <span style="color: green; font-weight: bold;"><?php echo $success_count; ?></span></p>
            <p><strong>Failed:</strong> <span style="color: red; font-weight: bold;"><?php echo $error_count; ?></span></p>
        </div>
        
        <?php if ($success_count > 0): ?>
        <div class="success">
            ✓ Successfully inserted <?php echo $success_count; ?> products into the database!
        </div>
        <?php endif; ?>
        
        <?php if ($error_count > 0): ?>
        <div class="error">
            ✗ Failed to insert <?php echo $error_count; ?> products.
            <div class="error-list">
                <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <a href="admin/admin_products.php" class="back-btn">Back to Products</a>
    </div>
</body>
</html>
