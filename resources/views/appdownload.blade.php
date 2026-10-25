<!doctype html>
<html lang="hi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sheetu Food — Smart Meal Tracking</title>
  <meta name="description" content="Sheetu App — Easily track your food schedule and payments." />
  
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --accent: #ff6600;
      --accent-light: #fff5ed;
      --text-dark: #0f172a;
      --text-muted: #64748b;
      --bg: #ffffff;
      --glass: rgba(255, 255, 255, 0.8);
    }

    * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
    
    body {
      margin: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg);
      color: var(--text-dark);
      line-height: 1.6;
    }

    /* Background Glows */
    .bg-glow {
      position: fixed;
      top: 0; left: 0; width: 100%; height: 100%;
      background: radial-gradient(circle at 90% 10%, rgba(255, 102, 0, 0.05), transparent 40%),
                  radial-gradient(circle at 10% 90%, rgba(255, 102, 0, 0.03), transparent 40%);
      z-index: -1;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Header */
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 0 20px 0;
    }

    .brand { display: flex; align-items: center; gap: 15px; }
    .logo { 
      width: 56px; height: 56px; border-radius: 16px; 
      box-shadow: 0 8px 20px rgba(255, 102, 0, 0.15);
      object-fit: cover;
    }
    .brand-text h1 { margin: 0; font-size: 20px; font-weight: 800; color: var(--text-dark); }
    .brand-text p { margin: 0; font-size: 12px; color: var(--accent); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

    /* Hero Section */
    .hero {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 50px;
      align-items: center;
      padding: 10px 0;
    }

    .headline {
      font-size: clamp(32px, 5vw, 48px);
      font-weight: 800;
      line-height: 1.2;
      margin-top: 0;
      margin-bottom: 20px;
      color: var(--text-dark);
    }

    .headline span { color: var(--accent); }

    .lead {
      font-size: 18px;
      color: var(--text-muted);
      margin-bottom: 35px;
      max-width: 500px;
    }

    /* Modern Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      background: var(--text-dark);
      color: white;
      padding: 16px 32px;
      border-radius: 18px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(255, 102, 0, 0.2);
      background: var(--accent);
    }

    /* Features Grid */
    .features {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      margin-top: 40px;
    }

    .feature {
      background: white;
      padding: 20px;
      border-radius: 20px;
      border: 1px solid #f1f5f9;
      transition: all 0.3s ease;
    }

    .feature:hover { border-color: var(--accent); background: var(--accent-light); }
    .feature h4 { margin: 0 0 8px 0; font-size: 15px; font-weight: 700; }
    .feature p { margin: 0; color: var(--text-muted); font-size: 13px; line-height: 1.5; }

    /* Mockup/Slider */
    .right-content { position: relative; }
    
    .screenshot-frame {
      position: relative;
      width: 300px;
      height: 600px;
      margin: 0 auto;
      background: #000;
      border: 8px solid #1e293b;
      border-radius: 40px;
      box-shadow: 0 50px 100px -20px rgba(0,0,0,0.25);
      overflow: hidden;
    }

    .carousel { width: 100%; height: 100%; position: relative; }
    .slides { display: flex; transition: transform 0.6s cubic-bezier(0.65, 0, 0.35, 1); height: 100%; }
    .slide { min-width: 100%; height: 100%; }
    .slide img { width: 100%; height: 100%; object-fit: cover; }

    .dots {
      position: absolute;
      bottom: 25px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
      padding: 8px 16px;
      background: rgba(0,0,0,0.3);
      backdrop-filter: blur(10px);
      border-radius: 20px;
    }

    .dot { width: 6px; height: 6px; background: rgba(255,255,255,0.4); border-radius: 50%; cursor: pointer; transition: 0.3s; }
    .dot.active { width: 20px; background: white; border-radius: 10px; }

    /* Floating WhatsApp */
    .wa-float {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      background: #25d366;
      border-radius: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
      z-index: 100;
      transition: 0.3s;
    }
    .wa-float:hover { transform: scale(1.1) rotate(5deg); }

    .footer {
      margin-top: 60px;
      padding: 40px 0;
      text-align: center;
      border-top: 1px solid #f1f5f9;
      color: var(--text-muted);
      font-size: 14px;
    }

    /* Mobile Responsive */
    @media (max-width: 968px) {
      .hero { grid-template-columns: 1fr; text-align: center; gap: 60px; }
      .lead { margin: 0 auto 35px; }
      .features { text-align: left; }
    }

    @media (max-width: 480px) {
      .features { grid-template-columns: 1fr; }
      .headline { font-size: 36px; }
      .screenshot-frame { width: 260px; height: 520px; }
    }
  </style>
</head>

<body>
  <div class="bg-glow"></div>
  
  <div class="container">
    <header>
      <div class="brand">
        <img class="logo" src="https://res.cloudinary.com/daizpwrnu/image/upload/v1764119305/shetu_logo_f7g7gn.png" alt="Sheetu Food">
        <div class="brand-text">
          <h1>Sheetu Food</h1>
          <p>Smart Meal Manager</p>
        </div>
      </div>
    </header>

    <main class="hero">
      <div class="left-content">
        <h1 class="headline">Ab khana manage karein <span>smartly.</span></h1>
        <p class="lead">Apna meal schedule, payments aur subscriptions manage karein ek hi app se. Stress-free lifestyle ki shuruat karein.</p>
        
        <a href="https://sheetufoodapp.pacificnestcode.com/Sheetu Food.apk" class="btn" download>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.523 15.3414L20.355 16.9964C21.003 17.3734 21.003 18.2894 20.355 18.6664L13.355 22.7534C12.529 23.2354 11.471 23.2354 10.645 22.7534L3.645 18.6664C2.997 18.2894 2.997 17.3734 3.645 16.9964L6.477 15.3414L10.645 17.7734C11.471 18.2554 12.529 18.2554 13.355 17.7734L17.523 15.3414ZM12 1L21 6.25V13.75L12 19L3 13.75V6.25L12 1Z"/>
          </svg>
          Download APK
        </a>

        <div class="features">
          <div class="feature">
            <h4>Track Meals</h4>
            <p>Consumption history par nazar rakhein.</p>
          </div>
          <div class="feature">
            <h4>Easy Payments</h4>
            <p>One-click subscription management.</p>
          </div>
          <div class="feature">
            <h4>Smart Reminders</h4>
            <p>Khane ka waqt kabhi miss na karein.</p>
          </div>
          <div class="feature">
            <h4>Expense Reports</h4>
            <p>Monthly kharche ka pura hisab-kitab.</p>
          </div>
        </div>
      </div>

      <div class="right-content">
        <div class="screenshot-frame">
          <div class="carousel" id="carousel">
            <div class="slides" id="slides">
              <div class="slide"><img src="https://res.cloudinary.com/daizpwrnu/image/upload/v1764407598/Screenshot_2025-11-29-14-40-07-496_com.createunique.hostel_eqnaab.jpg" alt="Screenshot 1"></div>
              <div class="slide"><img src="https://res.cloudinary.com/daizpwrnu/image/upload/v1764407599/Screenshot_2025-11-29-14-40-13-212_com.createunique.hostel_zhaxc4.jpg" alt="Screenshot 2"></div>
              <div class="slide"><img src="https://res.cloudinary.com/daizpwrnu/image/upload/v1764407598/Screenshot_2025-11-29-14-40-20-707_com.createunique.hostel_hoxssl.jpg" alt="Screenshot 3"></div>
            </div>
            <div class="dots" id="dots"></div>
          </div>
        </div>
      </div>
    </main>

    <footer class="footer">
      <p>Made with ❤️ by <b>Sheetu Food</b> • &copy; <span id="year"></span></p>
    </footer>
  </div>

  <a href="https://wa.me/919798262703" class="wa-float" target="_blank">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
    </svg>
  </a>

  <script>
    document.getElementById("year").textContent = new Date().getFullYear();

    (function(){
      const slides = document.getElementById("slides");
      const slideCount = slides.children.length;
      const dotsWrap = document.getElementById("dots");
      let index = 0;

      for(let i=0; i<slideCount; i++){
        const d = document.createElement("div");
        d.className = "dot" + (i===0 ? " active" : "");
        d.addEventListener("click", () => goTo(i));
        dotsWrap.appendChild(d);
      }

      function goTo(i){
        index = i;
        slides.style.transform = `translateX(-${i * 100}%)`;
        Array.from(dotsWrap.children).forEach((dd, idx) => dd.classList.toggle("active", idx === i));
      }

      let autoplay = setInterval(() => { goTo((index + 1) % slideCount); }, 4000);
      
      const carousel = document.getElementById("carousel");
      carousel.addEventListener("mouseenter", () => clearInterval(autoplay));
      carousel.addEventListener("mouseleave", () => {
        autoplay = setInterval(() => { goTo((index + 1) % slideCount); }, 4000);
      });
    })();
  </script>
</body>
</html>