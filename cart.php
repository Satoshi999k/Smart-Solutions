<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'init_cart.php';

// Function to calculate total cart quantity
function getCartTotalQuantity() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    // Count unique products (items), not total quantity
    return count($_SESSION['cart']);
}

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if cart is empty
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartEmpty = empty($cart);

// Check if logged in
$profile_picture = "image/login-icon.png";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Query to get user's profile picture
    $query = "SELECT profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['profile_picture'])) {
            $profile_picture = $row['profile_picture'];
        }
    }
    $stmt->close();
}

// Fetch products from database instead of hardcoded array
$products = [];
if (!empty($cart)) {
    // Get all product IDs from cart
    $cart_ids = array_column($cart, 'id');
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
    
    // Build query with proper type string
    $query = "SELECT id, name, price, image FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        // Create type string dynamically
        $types = str_repeat('i', count($cart_ids));
        $stmt->bind_param($types, ...$cart_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Handle image path - if relative, prepend /ITP122/
            if (!empty($row['image']) && strpos($row['image'], '/') !== 0 && strpos($row['image'], 'http') !== 0) {
                $row['image'] = '/ITP122/' . $row['image'];
            }
            $products[] = $row;
        }
        $stmt->close();
    }
}

// Fallback: Define the products array (empty since we fetch from DB)
if (empty($products)) {
    $products = [
    ["id" => 1, "name" => "Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W", "price" => 25195.00, "image" => "image/Core_i7.png"],
    ["id" => 2, "name" => "Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W", "price" => 14795.00, "image" => "image/Core_i3.png"],
    ["id" => 3, "name" => "MSI Thin A15 B7UCX-084PH 15.6 / FHD 144Hz AMD RYZEN 5 7535HS/8GB/512GBSSD/RTX 2050 4GB/WIN11 Laptop", "price" => 38995.00, "image" => "image/msithin.png"],
    ["id" => 4, "name" => "Lenovo V15 G4 IRU 15.6 / FHD Intel Core i5- 1335U/8GB DDR4/512GB M.2 SSD Laptop MN", "price" => 29495.00, "image" => "image/idealpad.png"],
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
    ["id" => 35, "name" => "MSI NVIDIA® GeForce RTX 3060 Ventus 2X OC 12gb 192bit GDdr6 Gaming Videocard LHR", "price" => 31595.00, "image" => "image/MSI_RTX_3060_Ventus.png"],
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
}

// Check if there are items in the cart
// Load cart from database if user is logged in and session cart is empty or doesn't match
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Ensure tables exist first
    $createProductsTableQuery = "CREATE TABLE IF NOT EXISTS `products` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL,
      `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      `image` VARCHAR(255) DEFAULT NULL,
      `category` VARCHAR(100) DEFAULT NULL,
      `stock` INT DEFAULT 0,
      `description` TEXT,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($createProductsTableQuery);
    
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `shopping_cart` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `product_id` INT UNSIGNED NOT NULL,
      `quantity` INT NOT NULL DEFAULT 1,
      `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_product` (`user_id`, `product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($createTableQuery);
    
    // Query cart items from database (with simple SELECT instead of JOIN)
    $cartQuery = "SELECT product_id, quantity FROM shopping_cart WHERE user_id = ? ORDER BY added_at ASC";
    $cartStmt = $conn->prepare($cartQuery);
    
    if ($cartStmt) {
        $cartStmt->bind_param("i", $user_id);
        $cartStmt->execute();
        $cartResult = $cartStmt->get_result();
        
        // Build cart array from database
        $dbCart = [];
        while ($cartRow = $cartResult->fetch_assoc()) {
            $product_id = $cartRow['product_id'];
            $quantity = $cartRow['quantity'];
            
            // Find product in $products array
            foreach ($products as $product) {
                if ($product['id'] == $product_id) {
                    $dbCart[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity
                    ];
                    break;
                }
            }
        }
        $cartStmt->close();
        
        // Use database cart if it exists, otherwise use session cart
        if (!empty($dbCart)) {
            $_SESSION['cart'] = $dbCart;
        }
    }
    // If query fails, just use session cart
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Clean up cart - remove items with invalid/missing product data
if (!empty($cart)) {
    $cleanedCart = [];
    foreach ($cart as $item) {
        // Check if product exists in the products array
        $productExists = false;
        foreach ($products as $product) {
            if ($product['id'] == $item['id']) {
                $productExists = true;
                break;
            }
        }
        // Only keep items that have valid products
        if ($productExists) {
            $cleanedCart[] = $item;
        }
    }
    $_SESSION['cart'] = $cleanedCart;
    $cart = $cleanedCart;
}

$total = 0;
// Handle item removal
if (isset($_GET['remove'])) {
    $removeIndex = $_GET['remove'];
    
    // If user is logged in, also delete from database
    if (isset($_SESSION['user_id']) && isset($cart[$removeIndex])) {
        $user_id = $_SESSION['user_id'];
        $product_id = $cart[$removeIndex]['id'];
        
        // Reconnect to database for removal
        $conn = new mysqli("localhost", "root", "", "smartsolutions");
        $deleteQuery = "DELETE FROM shopping_cart WHERE user_id = ? AND product_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        
        if ($deleteStmt) {
            $deleteStmt->bind_param("ii", $user_id, $product_id);
            $deleteStmt->execute();
            $deleteStmt->close();
        }
        // If query fails (table doesn't exist), just remove from session
        $conn->close();
    }
    
    unset($_SESSION['cart'][$removeIndex]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    // Redirect after removing item (must be done before any output)
    header("Location: cart.php");
    exit;  // Ensure no further code is executed after redirection
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="css/design.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/animations.css?v=<?php echo time(); ?>" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta charset="UTF-8">
    <title>SHOPPING CART - SMARTSOLUTIONS</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .breadcrumb { padding: 18px 32px; font-size: 14px; color: #666; background: transparent; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .breadcrumb a { color: #0062F6; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 6px; }
        .breadcrumb a:hover { color: #0052D4; transform: translateX(4px); }
        .breadcrumb .material-icons { font-size: 20px; vertical-align: middle; }
        .breadcrumb span:not(.material-icons) { display: inline-flex; align-items: center; }
        @keyframes slideDownMenu {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #main-menu {
            animation: slideDownMenu 0.6s ease-out 0.3s both;
        }
        
        /* Modern Cart Table Styles */
        .cart-table-wrapper {
            background: white;
            border-radius: 16px;
            margin: 30px auto;
            width: calc(100% - 60px);
            max-width: 1200px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .cart-table-header {
            background: linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%);
            padding: 25px 30px;
            color: white;
            text-align: center;
        }
        
        .cart-table-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }
        
        .cart-table-header i {
            font-family: 'Material Icons';
            font-size: 28px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th, td {
            padding: 18px 20px;
            text-align: center;
            border-bottom: 1px solid #e8e8e8;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 700;
            color: #2c3e50;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            color: #34495e;
            font-size: 14px;
        }
        
        th:last-child,
        td:last-child {
            border-right: none;
        }
        
        .checkbox-col {
            width: 60px;
            text-align: center;
        }
        
        .checkbox-col input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #007BFF;
        }
        
        table img {
            width: 90px;
            height: 90px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }
        
        td:first-child {
            border-right: none;
            padding-right: 10px;
            width: 110px;
        }
        
        td:nth-child(2) {
            border-left: none;
            padding-left: 10px;
            text-align: left;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .remove-button {
            color: white;
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(244, 67, 54, 0.2);
        }
        
        .remove-button:hover {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            box-shadow: 0 4px 16px rgba(244, 67, 54, 0.3);
            transform: translateY(-2px);
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 8px;
        }
        
        .quantity-btn {
            width: 36px;
            height: 36px;
            border: 2px solid #e8e8e8;
            background-color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            transition: all 0.2s ease;
            color: #007BFF;
        }
        
        .quantity-btn:hover {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }
        
        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .total {
            text-align: right;
            margin: 0;
            font-weight: 700;
            color: #007BFF;
            font-size: 16px;
        }
        
        /* Cart Summary Section */
        .checkout-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 30px;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e8eef5 100%);
            border-radius: 12px;
            border-left: 4px solid #007BFF;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
        }
        
        .checkout-left {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .selected-count {
            font-size: 14px;
            color: #666;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .selected-count i {
            font-family: 'Material Icons';
            font-size: 18px;
            color: #007BFF;
        }
        
        .total-price {
            font-size: 28px;
            font-weight: 700;
            color: #007BFF;
        }
        
        .checkout-button-container {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        .checkout {
            background: linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkout i {
            font-family: 'Material Icons';
            font-size: 20px;
        }
        
        .checkout:hover {
            background: linear-gradient(45deg, #0056b3 0%, #003f87 25%, #002d63 50%, #003f87 75%, #0056b3 100%);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .continue-shopping {
            background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
            color: #2c3e50;
            border: none;
            padding: 14px 32px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .continue-shopping:hover {
            background: linear-gradient(135deg, #eeeeee 0%, #e0e0e0 100%);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        /* Empty Cart Message */
        .empty-cart {
            text-align: center;
            padding: 60px 30px;
            background: white;
            border-radius: 16px;
            margin: 30px auto;
            width: calc(100% - 60px);
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .empty-cart i {
            font-family: 'Material Icons';
            font-size: 72px;
            color: #bdbdbd;
            margin-bottom: 15px;
        }
        
        .empty-cart h3 {
            color: #2c3e50;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .empty-cart p {
            color: #999;
            font-size: 14px;
            margin: 0 0 30px 0;
        }
        
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            flex-shrink: 0;
        }
        .main-content {
            flex: 1;
        }
        footer.footer {
            margin-top: auto;
            margin-bottom: 0 !important;
        }
        .copyright {
            margin-top: 0 !important;
        }
        
        /* Material Icons class for proper rendering */
        .material-icon {
            font-family: 'Material Icons' !important;
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        .material-icon.small {
            font-size: 16px;
        }
        
        .remove-button .material-icon {
            font-size: 16px;
            margin-right: 4px;
        }
        
        /* Modern Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            top: 110%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border: 1px solid #e0e0e0;
            min-width: 200px;
            z-index: 1000;
        }
        
        .dropdown-content a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .dropdown-content a:hover {
            background: #f5f5f5;
            color: #0062F6;
            border-left-color: #0062F6;
        }
        
        .dropdown-content a .material-icons {
            font-size: 18px;
            margin-right: 12px;
            display: flex;
            align-items: center;
        }
        
        .profile-dropdown.active .dropdown-content {
            display: block;
            animation: slideDown 0.25s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <header>
    <div class="ssheader">
        <div class="logo">
            <img src="image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <img class="search-icon" src="image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="pages/location.php">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="pages/track.php"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="cart.php">
            <div class="cart">
                <img class="cart" src="image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
                <span class="cart-counter">
                    <?php echo getCartTotalQuantity(); ?>
                </span>
            </div>
        </a>

        <style>
            .cart {
                position: relative;
                display: inline-block;
            }

            .cart-counter {
                position: absolute;
                top: 20px; 
                right: -10px; 
                background-color: #6c757d;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 14px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            }
        </style>

       <div class="login profile-dropdown">
        <a href="javascript:void(0)" onclick="toggleDropdown(event)">
            <img class="login" 
                 src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                 alt="login-icon" 
                 style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                        width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                        height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
        </a>
        <div class="dropdown-content">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user/profile.php"><span class="material-icons">person</span>View Profile</a>
                <a href="user/edit-profile.php"><span class="material-icons">edit</span>Edit Profile</a>
                <a href="user/logout.php"><span class="material-icons">logout</span>Log Out</a>
            <?php endif; ?>
        </div>
        </div>
        <div class="login-text">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"></a>
            <?php else: ?>
                <a href="user/register.php"><p>Login/<br>Sign In</p></a>
            <?php endif; ?>
        </div>
    </div>
    </header>

<div class="main-content">
<div class="menu" id="main-menu">
    <a href="index.php">HOME</a>
    <a href="pages/product.php">PRODUCTS</a>
    <a href="products/desktop.php">DESKTOP</a>
    <a href="products/laptop.php">LAPTOP</a>
    <a href="pages/brands.php">BRANDS</a>
</div>

<div class="breadcrumb">
    <a href="index.php"><span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px;">home</span>Home</a> > <span class="material-icons" style="vertical-align: middle; margin-right: 8px; font-size: 20px; color: #0062F6;">shopping_cart</span><a>Shopping Cart</a>
</div>

<div class="main-content">
    <div class="cart-table-wrapper">
        <div class="cart-table-header">
            <h2><i class="material-icon">shopping_cart</i>Your Shopping Cart</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="checkbox-col"><input type="checkbox" id="select-all-checkbox" title="Select all items"></th>
                    <th colspan="2">Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
        <tbody>
            <?php if (!empty($cart)): ?>
                <?php foreach ($cart as $index => $item): ?>
                    <?php
                    // Find the product in the $products array
                    $product = array_filter($products, function($p) use ($item) {
                        return $p['id'] == $item['id'];
                    });
                    $product = reset($product);
                    
                    // Skip if product not found
                    if (!$product) continue;
                    
                    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                    $subtotal = $product['price'] * $quantity;
                    ?>
                    <tr>
                        <td class="checkbox-col">
                            <input type="checkbox" class="item-checkbox" 
                                   data-index="<?php echo $index; ?>" 
                                   data-price="<?php echo floatval($product['price']); ?>" 
                                   data-quantity="<?php echo intval($quantity); ?>"
                                   title="Price: <?php echo $product['price']; ?>, Qty: <?php echo $quantity; ?>">
                        </td>
                        <td>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $index; ?>, 'decrease')">−</button>
                                <span class="quantity-display" id="qty-<?php echo $index; ?>"><?php echo $quantity; ?></span>
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $index; ?>, 'increase')">+</button>
                            </div>
                        </td>
                        <td><span id="subtotal-<?php echo $index; ?>">₱<?php echo number_format($subtotal, 2); ?></span></td>
                        <td>
                            <a href="cart.php?remove=<?php echo $index; ?>" style="text-decoration: none;">
                                <button class="remove-button" title="Remove item from cart"><i class="material-icon">delete</i></button>
                            </a>
                        </td>
                    </tr>
                    <?php $total += $subtotal; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-cart">
                            <i class="material-icon" style="font-size: 72px; margin-bottom: 15px; display: block;">shopping_cart</i>
                            <h3>Your Cart is Empty</h3>
                            <p>You haven't added any items yet. Start shopping to find great deals on computer parts!</p>
                            <a href="index.php" class="continue-shopping"><i class="material-icon">arrow_back</i>Continue Shopping</a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="checkout-info">
        <div class="checkout-left">
            <span class="selected-count"><i class="material-icon small">shopping_bag</i>Selected: <strong id="selected-count">0</strong> item(s)</span>
            <div class="total-price">Total: <span id="selected-total">₱0.00</span></div>
        </div>
        <div class="checkout-button-container">
            <?php if ($cartEmpty): ?>
                <button class="checkout" disabled style="opacity: 0.5; cursor: not-allowed; pointer-events: none;" title="Your cart is empty">
                    <i class="material-icon">lock</i>Proceed to Checkout
                </button>
            <?php else: ?>
                <button class="checkout" id="checkout-btn" type="button" onclick="proceedToCheckout(); return false;">
                    <i class="material-icon">payment</i>Proceed to Checkout
                </button>
            <?php endif; ?>
            <a href="index.php" class="continue-shopping"><i class="material-icon">arrow_back</i>Continue Shopping</a>
        </div>
    </div>
</div>
    <footer class="footer">
    <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="pages/paymentfaq.php">Payment FAQs</a></li>
            <li><a href="pages/ret&ref.php">Return and Refunds</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Company</h3>
        <ul>
            <li><a href="pages/about_us.php">About Us</a></li>
            <li><a href="pages/contact_us.php">Contact Us</a></li>
        </ul>
    </div>
    <div class="footer-col">
        <h3>Links</h3>
        <ul>
            <li><a href="pages/corporate.php">SmartSolutions Corporate</a></li>
            <li><a href="https://web.facebook.com/groups/1581265395842396" target="_blank">SmartSolutions Community</a></li>
        </ul>
    </div>
    <div class="footer-col footer-logo">
        <h4>Follow Us</h4>
        <img src="image/logo.png" alt="Company Logo">
        <div class="social-media">
                <a href="https://web.facebook.com/smartsolutionscomputershop.ph" target="_blank">
                    <img src="image/facebook.png" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/smartsolutions.ph/" target="_blank">
                    <img src="image/instagram.png" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/@smrtsolutioncomputershop" target="_blank">
                    <img src="image/tiktok.png" alt="Tiktok">
                </a>
                <a href="https://www.linkedin.com/in/smart-solutions-40b512339/" target="_blank">
                    <img src="image/linkedin.png" alt="LinkedIn">
                </a>
                <a href="https://www.youtube.com/@SmartSolutions-o4j/" target="_blank">
                    <img src="image/youtube.png" alt="YouTube">
                </a>
            </div>
    </div>
</footer>

<div class="copyright">
    &copy; 2024 SmartSolutions. All rights reserved.
</div>

<script>
        // Modern dropdown toggle function
        function toggleDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            const profileDropdown = document.querySelector('.profile-dropdown');
            profileDropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('active');
            }
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const profileDropdown = document.querySelector('.profile-dropdown');
                if (profileDropdown) {
                    profileDropdown.classList.remove('active');
                }
            }
        });

        // Function to update cart quantity
        function updateQuantity(itemIndex, action) {
            // Create a form data object
            var formData = new FormData();
            formData.append('item_index', itemIndex);
            formData.append('action', action);

            // Send AJAX request to update_cart_quantity.php
            fetch('update_cart_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the quantity display
                    document.getElementById('qty-' + itemIndex).textContent = data.new_quantity;

                    // Get the price from the checkbox data attribute
                    var row = document.getElementById('qty-' + itemIndex).closest('tr');
                    var checkbox = row.querySelector('.item-checkbox');
                    var price = parseFloat(checkbox.getAttribute('data-price'));

                    // Update the subtotal in real-time
                    if (!isNaN(price)) {
                        var newSubtotal = price * data.new_quantity;
                        var formattedSubtotal = '₱' + newSubtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        document.getElementById('subtotal-' + itemIndex).textContent = formattedSubtotal;
                    }

                    // Update the checkbox data-quantity attribute
                    if (checkbox) {
                        checkbox.setAttribute('data-quantity', data.new_quantity);
                    }
                    
                    // Refresh the selected total if this item is checked
                    if (checkbox && checkbox.checked) {
                        updateSelectedInfo();
                    }

                    // Update cart counter if quantity is 0
                    if (data.new_quantity === 0) {
                        location.reload();
                    }
                } else {
                    alert('Error updating quantity: ' + data.message);
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the quantity.');
                location.reload();
            });
        }
    </script>

    <!-- Empty Cart Validation Script -->
    <script>
    // Update selected count and total when checkboxes change - GLOBAL FUNCTION
    function updateSelectedInfo() {
        let selectedCount = 0;
        let selectedTotal = 0;
        
        // Get all checkboxes
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        console.log('Total checkboxes found:', allCheckboxes.length);
        
        allCheckboxes.forEach((checkbox, idx) => {
            console.log(`Checkbox ${idx}: checked=${checkbox.checked}, data-price=${checkbox.getAttribute('data-price')}, data-quantity=${checkbox.getAttribute('data-quantity')}`);
            
            if (checkbox.checked) {
                selectedCount++;
                
                // Get data attributes safely
                const priceAttr = checkbox.getAttribute('data-price');
                const quantityAttr = checkbox.getAttribute('data-quantity');
                
                console.log(`  -> Selected! Price attr: "${priceAttr}", Qty attr: "${quantityAttr}"`);
                
                const price = parseFloat(priceAttr);
                const quantity = parseInt(quantityAttr);
                
                console.log(`  -> Parsed: price=${price}, qty=${quantity}`);
                
                // Only add if both are valid numbers
                if (!isNaN(price) && !isNaN(quantity)) {
                    selectedTotal += (price * quantity);
                    console.log(`  -> Added to total: ${price} * ${quantity} = ${price * quantity}, Running total: ${selectedTotal}`);
                }
            }
        });
        
        console.log('Final - Selected count:', selectedCount, 'Selected total:', selectedTotal);
        
        // Update the display elements
        const countEl = document.getElementById('selected-count');
        const totalEl = document.getElementById('selected-total');
        
        if (countEl) {
            countEl.textContent = selectedCount;
        }
        
        if (totalEl) {
            const formattedTotal = '₱' + selectedTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            totalEl.textContent = formattedTotal;
            console.log('Updated total display to:', formattedTotal);
        }
        
        // Update checkout button state
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            if (selectedCount === 0) {
                checkoutBtn.disabled = true;
                checkoutBtn.style.opacity = '0.5';
                checkoutBtn.style.cursor = 'not-allowed';
            } else {
                checkoutBtn.disabled = false;
                checkoutBtn.style.opacity = '1';
                checkoutBtn.style.cursor = 'pointer';
            }
        }
    }
    
    $(document).ready(function() {
        // Handle individual item checkbox with immediate update
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedInfo();
                updateSelectAllCheckbox();
            });
        });
        
        // Handle select all checkbox
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateSelectedInfo();
            });
        }
        
        // Update select all checkbox state
        function updateSelectAllCheckbox() {
            const totalCheckboxes = document.querySelectorAll('.item-checkbox').length;
            const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked').length;
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;
                selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
            }
        }
        
        // Initialize with a slight delay to ensure DOM is ready
        setTimeout(function() {
            updateSelectedInfo();
        }, 100);
    });
    
    // Proceed to checkout with selected items
    // Function to show ARIA live alert
    function showAriaAlert(message, redirect = null) {
        let alertDiv = document.getElementById('aria-alert');
        if (!alertDiv) {
            alertDiv = document.createElement('div');
            alertDiv.id = 'aria-alert';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.setAttribute('aria-live', 'assertive');
            alertDiv.setAttribute('aria-atomic', 'true');
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '30px';
            alertDiv.style.right = '30px';
            alertDiv.style.backgroundColor = '#fff3cd';
            alertDiv.style.color = '#856404';
            alertDiv.style.padding = '16px 24px';
            alertDiv.style.borderRadius = '8px';
            alertDiv.style.border = '2px solid #ffc107';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.minWidth = '350px';
            alertDiv.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            alertDiv.style.fontWeight = '500';
            alertDiv.style.fontSize = '14px';
            alertDiv.style.fontFamily = 'Arial, sans-serif';
            alertDiv.style.lineHeight = '1.5';
            alertDiv.style.animation = 'slideIn 0.3s ease-out';
            document.body.appendChild(alertDiv);
        }
        alertDiv.textContent = message;
        alertDiv.style.display = 'block';
        
        // Add animation styles
        if (!document.getElementById('alert-styles')) {
            const style = document.createElement('style');
            style.id = 'alert-styles';
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                }
                #aria-alert {
                    transition: all 0.3s ease-out;
                }
                #aria-alert.hide {
                    animation: slideOut 0.3s ease-out;
                }
            `;
            document.head.appendChild(style);
        }
        
        if (redirect) {
            setTimeout(() => {
                alertDiv.classList.add('hide');
                setTimeout(() => {
                    window.location.href = redirect;
                }, 300);
            }, 2000);
        } else {
            setTimeout(() => {
                alertDiv.classList.add('hide');
                setTimeout(() => {
                    alertDiv.style.display = 'none';
                    alertDiv.classList.remove('hide');
                }, 300);
            }, 3000);
        }
    }

    function proceedToCheckout() {
        // Check if user is logged in
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        
        if (!isLoggedIn) {
            showAriaAlert('Please create an account or login first to proceed to checkout.', 'user/register.php');
            return;
        }
        
        const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            showAriaAlert('Please select at least one item to checkout.');
            return;
        }
        
        // Store selected indices
        const selectedIndices = Array.from(selectedCheckboxes).map(cb => cb.getAttribute('data-index'));
        
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'pages/checkout.php';  // Point to pages/checkout.php, not root checkout.php
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_items';
        input.value = JSON.stringify(selectedIndices);
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
    
    // Function to check if cart is empty
    function isCartEmpty() {
        // Check session variable in PHP
        var cartEmpty = <?php echo json_encode($cartEmpty); ?>;
        return cartEmpty;
    }

    // Disable checkout button if cart is empty
    function updateCheckoutButton() {
        var $checkoutBtn = $('.checkout');
        var isEmpty = isCartEmpty();
        
        if (isEmpty) {
            $checkoutBtn.prop('disabled', true)
                .css({
                    'opacity': '0.5',
                    'cursor': 'not-allowed',
                    'pointer-events': 'none'
                })
                .attr('title', 'Your cart is empty. Please add items to checkout.');
            
            // Prevent any checkout attempts
            $checkoutBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Show alert
                alert('Your cart is empty! Please add items before checking out.');
                return false;
            });
        }
    }

    // Initialize on page load
    updateCheckoutButton();

    // Monitor cart changes
    $(document).on('change', '.quantity-input, #qty-input', function() {
        setTimeout(updateCheckoutButton, 500);
    });

    // Handle page refresh after removing items
    $(window).on('beforeunload', function() {
        // Reload to sync cart state
        return undefined;
    });
    </script>

    <!-- Cart Validation Script Include -->
    <script src="jquery-animations.js"></script>
    <script src="cart-validation.js"></script>
<script src="search.js"></script>
</body>
</html>
