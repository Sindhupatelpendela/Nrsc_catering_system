# NRSC DIKSTRA Service Portal - User Manual & Working Procedure

## 1. How to Start the Application
Since the files are on your Desktop, the easiest way to run this is using the built-in PHP server.
1. Open your Command Prompt / Terminal.
2. Navigate to the project folder:
   `cd "c:\Users\hp\OneDrive\Desktop\NRSC-Catering-System"`
3. Run this command:
   `php -S localhost:8000`
4. Open your browser (Chrome/Edge) and go to:
   `http://localhost:8000`

---

## 2. Working Procedure (Step-by-Step Flow)

### Phase 1: Employee - Creating a Request
1. **Login** as an Employee (Username: `emp01`, Password: `password`).
2. You will see the **Dashboard** with options for "Catering" and "Photography".
3. **Catering Request**:
   - Click "New Catering Req".
   - Fill in Meeting details (Name, Time, Venue) and add Items (Coffee, Tea, etc.).
   - Click **Submit**.
4. **Photography Request**:
   - Click "New Photo/Video Req".
   - Fill in Event details and select Media Type (Photo/Video).
   - Click **Submit**.
5. **Track Status**: Go to "My Requests" to see if your request is Pending, Approved, or Rejected.

### Phase 2: Officer - Approval Process
1. **Login** as an Officer (Username: `off01`, Password: `password`).
2. Go to **Pending Approvals**. You will see a combined list of Catering and Media requests.
3. **Action**:
   - **Approve**: Click the "Approve" button to forward it to the Canteen/Media Dept.
   - **Reject**: Click "Reject", enter the **Reason/Remarks** for rejection, and confirm.
   - You can view past decisions in the "Approval History" tab.

### Phase 3: Canteen - Sanction & Fulfillment
1. **Login** as Canteen Manager (Username: `canteen01`, Password: `password`).
2. **New Orders Tab**:
   - You will see orders deemed "Approved" by the Officer.
   - **Action**: Click **Sanction** to accept the order into your kitchen queue, OR **Reject** if unavailable.
3. **Sanctioned List Tab**:
   - These are orders currently being prepared ("Processing").
   - Once served, click **Served/Completed**.
4. **History Tab**: View all completed or rejected orders.

---

## 3. Account Recovery
- If any user forgets their password, they can click **"Forgot Password?"** on the login screen.
- Enter the username.
- A "Simulation Link" will appear (since this is offline). Click it to reset the password.

## 4. Default Credentials
| Role | Username | Password |
|------|----------|----------|
| **Admin** | `admin` | `password` |
| **Employee** | `emp01` | `password` |
| **Officer** | `off01` | `password` |
| **Canteen** | `canteen01` | `password` |
