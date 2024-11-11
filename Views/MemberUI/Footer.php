<!-- Footer -->
 <style>
      #fb,
  #tw,
  #git,
  #ig {
    color: #fff;
    background-color: #18191f;
    font-size: 24px;
    box-shadow: 2px 2px 2px #00000080, 10px 1px 12px #00000080,
      2px 2px 10px #00000080, 2px, 2px, 3px #00000080, inset 2px 2px 10px #000008,
      inset 2px 2px 10px #000008, inset 2px 2px 10px #000008,
      inset 2px 2px 10px #000008;
    border-radius: 5px;
    padding: 5px 5px;
    margin: 20px 20px 0 0;
    animation: animate 3s linear infinite;
    text-shadow: 0 0 20px #0072ff, 0 0 50px #0072ff, 0 0 70px #0072ff,
      0 0 100px #0072ff;
  }

  #tw {
    animation-delay: 0.7s;
  }
  #ig {
    background-color: #18191f;

    animation-delay: 0.5s;
  }
  
  #git {
    animation-delay: 0, 2s;
  }

  @keyframes animate {
    from {
      filter: hue-rotate(0deg);
    }
    to {
      filter: hue-rotate(360deg);
    }
  }
 </style>
<footer id="footer" class="bg-light py-5">
    <div class="container">
        <div class="row d-flex justify-content-around" style="height: fit-content;">
            <!-- Contact Info -->
            <div class="col-12 col-md-3 mb-4">
                <h5>Thông tin liên hệ</h5>
                <p>
                    <i class="fa-solid fa-location-dot me-2"></i>
                    An Dương Vương, Phường 3, Quận 5
                </p>
                <p>
                    <i class="fa-solid fa-phone-volume me-2"></i>
                    0325459901
                </p>
                <p>
                    <i class="fa-solid fa-envelope me-2"></i>
                    hungnt.020404@gmail.com
                </p>
            </div>



            <!-- Follow Us -->
            <div class="col-12 col-md-3 mb-4">
                <h5>FOLLOW US</h5>
                <a href="https://www.facebook.com/doanhdai.2004"><i id="fb" class="fa-brands fa-facebook" id="fb"></i></a>
                <a href="https://www.instagram.com"><i id="ig" class="fa-brands fa-instagram"></i></a>
                <a href="https://github.com/ltgiai/DO_AN_WEBSITE/tree/main"><i id="git" class="fa-brands fa-github"></i></a>
                <a href="https://twitter.com/?lang=vi"><i id="tw" class="fa-brands fa-square-twitter"></i></a>
                <a href="http://online.gov.vn/Home/WebDetails/36260"><img src="#" alt="" /></a>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="row" style="height: fit-content;">
            <div class="col-12 text-center">
                <p class="mt-4 mb-0">Copyrights © 2019 by Thug24. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>