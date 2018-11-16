/* 
 * Обработчики элементов управления скоростью воспроизведения видеоплеера
 */
let mv_video_speed = document.getElementsByName("mv_video_speed");

for (let i = 0; i < mv_video_speed.length; i++) {
    mv_video_speed[i].onclick = function (event) {
        document.querySelector('video').playbackRate = mv_video_speed[i].value;
    };
};