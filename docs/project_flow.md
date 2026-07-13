# FloraFetch - Project Flow & Implementation Plan

Ye document define karta hai ke FloraFetch project kis tarah kaam karega, ismein kaunsi technology use hogi, aur iska complete flow kya hoga. Student ke viva aur project ki requirements ke mutabiq isko bohat simple aur clean rakha gaya hai.

## 1. Technology Stack
*   **Frontend (Design & UI):** HTML5, CSS3, JavaScript, aur Bootstrap 5 (Responsive design ke liye).
*   **Backend (Logic):** Core PHP (Procedural). Hum koi complex framework use nahi karenge taake viva mein samjhana asaan ho.
*   **Database:** MySQL.
*   **Server:** XAMPP (Apache & MySQL).

## 2. Project Flow (Ye kaam kaise karega)

Project ko do (2) main hisson mein taqseem kiya gaya hai: **User Module** aur **Admin Module**.

### A. User / Customer Flow
1.  **Home & Shop:** User website par aayega, `index.php` aur `shop.php` par usay mukhtalif pauday (plants) nazar aayenge. Wo categories (Indoor, Outdoor) ke hisaab se filter kar sakega.
2.  **Plant Details:** Kisi plant par click karne se `plant_detail.php` khulega jahan us plant ki qeemat, dhoop aur paani ki zaroorat (Care Guide) likhi hogi.
3.  **Add to Cart:** User plant ko cart mein add karega. Ye cart data PHP `$_SESSION` mein save hoga jab tak user order place nahi karta.
4.  **Login / Register:** Agar user logged in nahi hai, toh checkout se pehle usay `login.php` ya `register.php` par bheja jayega.
5.  **Checkout (Cash on Delivery):** User apna delivery address dega aur order confirm karega. Data MySQL ki `orders` table mein chala jayega.
6.  **My Orders:** User `my_orders.php` par ja kar apne order ka status (Pending, Delivered) dekh sakega.

### B. Admin Flow
1.  **Admin Login:** Admin apne specific credentials se login karega.
2.  **Dashboard:** `admin/index.php` par usay total orders, total plants aur users ki tadaad nazar aayegi.
3.  **Manage Plants:** Admin naye pauday add kar sakega, unki tasweerein upload kar sakega aur qeemat tabdeel kar tabdeel kar sakega. (Tasweerein `assets/uploads/` folder mein jayengi aur unka naam database mein save hoga).
4.  **Manage Orders:** Admin ke paas sab customers ke orders aayenge. Admin wahan se order ka status update karega (e.g., "In Transit" ya "Delivered").

## 3. Database Architecture (Basic Schema Idea)
Hum MySQL mein ek database `florafetch_db` banayenge jisme ye 4 bunyadi tables hongi:
*   **`users`**: Customer aur Admin ka data (name, email, password, role).
*   **`plants`**: Paudon ka data (name, category, price, care_guide, image).
*   **`orders`**: Order ki detail (user_id, total_price, status, delivery_address).
*   **`order_items`**: Ek order mein kaun kaun se plants khareeday gaye.
