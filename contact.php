<?php
// 資料庫連接設定
$servername = "localhost";
$username = "CS380B";
$password = "YZUCS380B";
$dbname = "CS380B";

// 創建資料庫連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查資料庫連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 撰寫 SQL 查詢來獲取募款人數和總募資金額
$sql = "SELECT COUNT(*) AS total_donors, SUM(amount) AS total_amount FROM s1131408";
$result = $conn->query($sql);

// 檢查結果並顯示
if ($result->num_rows > 0) {
    // 輸出數據
    $row = $result->fetch_assoc();
    $total_donors = $row['total_donors'];
    $total_amount = $row['total_amount'];
} else {
    $total_donors = 0;
    $total_amount = 0;
}

// 關閉資料庫連接
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- site metas -->
      <title>hightech</title>
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" href="css/style.css">
      <!-- responsive-->
      <link rel="stylesheet" href="css/responsive.css">
      <!-- awesome fontfamily -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   </head>
   <body class="main-layout inner_page">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="" /></div>
      </div>
      <!-- end loader -->
      <!-- header -->
      <header>
         <div class="header">
            <div class="container-fluid">
               <div class="row d_flex">
                  <div class="col-md-2 col-sm-3 col logo_section">
                     <div class="full">
                        <div class="center-desk">
                           <div class="logo">
                              <a href="index.php"><img src="images/logo.png" alt="#" /></a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-8 col-sm-9">
                     <nav class="navigation navbar navbar-expand-md navbar-dark">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarsExample04">
                           <ul class="navbar-nav mr-auto">
                              <li class="nav-item active">
                                 <a class="nav-link" href="index.php">Home</a>
                              </li>
                              <li class="nav-item">
                                 <a class="nav-link" href="contact.php">Contact Us</a>
                              </li>
                           </ul>
                        </div>
                     </nav>
                  </div>
               </div>
            </div>
         </div>
      </header>
      <!-- end header -->

      <!-- contact -->
      <div class="contact">
          <div class="container">
              <div class="row">
                  <div class="col-md-8 offset-md-2">
                      <div class="titlepage text_align_left">
                          <h2>Get In Touch</h2>
                      </div>
                      <form id="request" action="s1131408_HW2.php" method="post" class="main_form">                        
                          <div class="col-md-12">
                              <label>Donation Amount:</label>
                              <div>
                                  <input type="checkbox" id="amount1" name="Amount" value="200" onclick="checkOnlyOne(this)"> $200 
                                  <input type="checkbox" id="amount2" name="Amount" value="500" onclick="checkOnlyOne(this)"> $500 
                                  <input type="checkbox" id="amount3" name="Amount" value="1000" onclick="checkOnlyOne(this)"> $1000 
                                  <input type="checkbox" id="amount4" name="Amount" value="2000" onclick="checkOnlyOne(this)"> $2000 
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-12">
                                  <input class="contactus" placeholder="Name" type="text" name="Name" required pattern="^[\u4e00-\u9fa5A-Za-z\s]+$" title="Please enter your name in Chinese or English">
                              </div>
                              <div class="col-md-12">
                                  <input class="contactus" placeholder="Phone Number" type="tel" name="PhoneNumber" required pattern="^09\d{8}$" title="Please enter a valid phone number">
                              </div>
                              <div class="col-md-12">
                                  <input class="contactus" placeholder="Email" type="email" name="Email" required title="Please enter a valid email address">
                              </div>
                              <div class="col-md-12">
                                  <textarea class="textarea" placeholder="Message" name="Txtarea"></textarea>
                              </div>
                              <div class="col-md-12">
                                  <button class="send_btn" type="submit">Send Now</button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
              <div class="statistics text-center" style="margin-top: 40px;">
                  <h3>Fundraising Statistics</h3>
                  <p><strong>Total Donors:</strong> <?php echo $total_donors; ?> people</p>
                  <p><strong>Total Raised:</strong> $<?php echo number_format($total_amount); ?></p>
              </div>
          </div>
      </div>

      <script>
          function checkOnlyOne(checkbox) {
              const checkboxes = document.getElementsByName('Amount');
              checkboxes.forEach((item) => {
                  if (item !== checkbox) item.checked = false;
              });
          }
      </script>

      <!-- footer -->
      <footer>
         <div class="footer">
            <div class="container">
               <div class="row">
                  <div class="col-md-3">
                     <a class="logo_footer" href="index.php"><img src="images/logo_footer.png" alt="#" /></a>
                  </div>
                  <div class="col-md-9">
                     <form class="newslatter_form">
                        <button class="subs_btn">Subscribe Now</button>
                     </form>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="Informa helpful">
                        <h3>Useful  Link</h3>
                        <ul>
                           <li><a href="index.php">Home</a></li>
                           <!-- <li><a href="about.php">About</a></li>
                           <li><a href="we_do.php">What we do</a></li>
                           <li><a href="portfolio.php">Portfolio</a></li> -->
                           <li><a href="contact.php">Contact us</a></li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="Informa">
                        <h3>News</h3>
                        <ul>
                           <li>It is a long established                            
                           </li>
                           <li>fact that a reader will                           
                           </li>
                           <li>be distracted by the                           
                           </li>
                           <li>readable content of a                              
                           </li>
                           <li>page when                          
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="Informa">
                        <h3>company</h3>
                        <ul>
                           <li>It is a long established                             
                           </li>
                           <li>fact that a reader will                            
                           </li>
                           <li>be distracted by the                          
                           </li>
                           <li>readable content of a                              
                           </li>
                           <li>page when                          
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="Informa conta">
                        <h3>contact Us</h3>
                        <ul>
                           <li> <a href="Javascript:void(0)"> <i class="fa fa-map-marker" aria-hidden="true"></i> Location
                              </a>
                           </li>
                           <li> <a href="Javascript:void(0)"><i class="fa fa-phone" aria-hidden="true"></i> Call +01 1234567890
                              </a>
                           </li>
                           <li> <a href="Javascript:void(0)"> <i class="fa fa-envelope" aria-hidden="true"></i> demo@gmail.com
                              </a>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
            <div class="copyright text_align_left">
               <div class="container">
                  <div class="row d_flex">
                     <div class="col-md-6">
                        <p>© 2020 All Rights Reserved.  <a href="https://html.design/"> Free Html Template</a></p>
                     </div>
                     <div class="col-md-6">
                        <ul class="social_icon text_align_center">
                           <li> <a href="Javascript:void(0)"><i class="fa fa-facebook-f"></i></a></li>
                           <li> <a href="Javascript:void(0)"><i class="fa fa-twitter"></i></a></li>
                           <li> <a href="Javascript:void(0)"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>
                           <li> <a href="Javascript:void(0)"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                           <li> <a href="Javascript:void(0)"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </footer>
      <!-- end footer -->

      <!-- Javascript files-->
      <script src="js/jquery.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.0.0.min.js"></script>
      <script src="js/custom.js"></script>
   </body>
</html>