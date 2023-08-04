<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Phát nhạc</title>
<style>
  /* Ẩn trình phát âm thanh */
  audio {
    display: none;
  }
</style>
</head>
<body>

<select id="songSelect">
  <option value="ding.mp3">Bài hát Ding</option>
  <!-- Các tùy chọn khác -->
</select>

<audio id="audioPlayer" controls>
  <source id="audioSource" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>

<button id="playButton">Nghe thử</button>

<script>
  const audio = document.getElementById('audioPlayer');
  const audioSource = document.getElementById('audioSource');
  const songSelect = document.getElementById('songSelect');
  const playButton = document.getElementById('playButton');

  playButton.addEventListener('click', () => {
    if (audio.paused) {
      const selectedSong = songSelect.value;
      
      // Gửi giá trị đường dẫn tới tệp PHP để lấy mã Base64
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'Listen.php?song=' + selectedSong, true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const base64Audio = xhr.responseText;
          audioSource.src = "data:audio/mpeg;base64," + base64Audio;
          audio.load();
          audio.play();
        }
      };
      xhr.send();
    } else {
      audio.pause();
      audio.currentTime = 0;
    }
  });
</script>

</body>
</html>
