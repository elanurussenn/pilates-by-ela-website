
const slider = document.querySelector('.one-cikan-slider');
const btnLeft = document.querySelector('.slider-btn.left');
const btnRight = document.querySelector('.slider-btn.right');

btnLeft.addEventListener('click', () => {
    slider.scrollBy({ left: -700, behavior: 'smooth' });
});

btnRight.addEventListener('click', () => {
    slider.scrollBy({ left: 700, behavior: 'smooth' });
});


 document.querySelector("a[href='#yorumlar']").addEventListener("click", function() {
        const yorumDiv = document.getElementById("yorumlar");
        yorumDiv.style.display = yorumDiv.style.display === "none" || yorumDiv.style.display === "" ? "block" : "none";
    });

/*hocam tum js kodları bu kadar degıl cogu yerde php sayfalarının ıcınde mevcut ozellıkle
butonlarda,guncelleme ve sılme ıslemlerınde cok falza js dosyası kullandım */