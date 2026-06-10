<?php
// update_db_v4_referral_logic.php
require_once 'db_config.php';

echo "Starting Database Migration v4 (Referral Logic Update)...\n";

// 1. Add referral_rewarded column to users table
$sql1 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS referral_rewarded BOOLEAN DEFAULT FALSE AFTER referred_by";
if ($conn->query($sql1)) {
    echo "✓ Added 'referral_rewarded' column to users table.\n";
} else {
    echo "✗ Error adding 'referral_rewarded' column: " . $conn->error . "\n";
}

// 2. Add columns to orders table if missing (based on previous updates)
$sql2 = "ALTER TABLE orders ADD COLUMN IF NOT EXISTS coins_used INT DEFAULT 0 AFTER coins_earned,
                            ADD COLUMN IF NOT EXISTS coin_discount DECIMAL(10,2) DEFAULT 0.00 AFTER coins_used";
if ($conn->query($sql2)) {
    echo "✓ Ensured 'coins_used' and 'coin_discount' columns in orders table.\n";
}

// 3. Ensure 'referrals' table has status column
$sql3 = "ALTER TABLE referrals ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'pending' AFTER reward_amount";
if ($conn->query($sql3)) {
    echo "✓ Ensured 'status' column in referrals table.\n";
}

echo "Migration Complete.\n";
$conn->close();
?>
