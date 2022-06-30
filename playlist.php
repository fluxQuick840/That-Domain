<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
.navigation {
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

.action-btn {
  border: 0;
  border-radius: 20px;
  font-size: 20px;
  cursor: pointer;
  padding: 10px;
  margin: 0 20px;
}

.action-btn.action-btn-big {
  font-size: 30px;
  outline: none;
}

.action-btn:focus {
  outline: 0;
}

.progress-container {
  background: #fff;
  border-radius: 5px;
  cursor: pointer;
  margin: 10px 0;
  height: 4px;
  width: 100%;
}

/* styling the actual progress bar and making it flow along with the song*/
.progress {
  background-color: #fe8daa;
  border-radius: 5px;
  height: 100%;
  width: 0%;
  transition: width 0.1s linear;
}
</style>
</head>
<div class="music-container" id="music-container">
<div class="music-info">
<div id="title" style="text-align:center;"></div>
<div id="progress" style="text-align:center;"></div>
</div>
</div>
<hr>
<audio id="audio"></audio>
<div class="navigation">
<button id="prev" class="action-btn"><i class="fas fa-backward"></i>Previous</button>
<button id="rw">Rewind</button>
<button id="play" class="action-btn action-btn-big"><i class="fas fa-play"></i>Play/Pause</button>
<button id="fw">Fast Forward</button>
<button id="next" class="action-btn"><i class="fas fa-forward"></i>Next</button>
<input type="range" min="1" max="100" value="100" id="volume" title="Volume">
</div>
<hr>
<div id="extras">
<details>
<summary>Tracks</summary>
<div id="tracksarea">
</div>
</details>
<dialog id="shortcuts">
<button id="closeKeyDialog">Close</button>
<h3>Keyboard Shortcuts</h3>
<table>
<tr><th>Action</th><th>Shortcut</th></tr>
<tr><td>Play/Pause</td><td>Space</td></tr>
<tr><td>Rewind</td><td>Left Arrow</td></tr>
<tr><td>Fast Forward</td><td>Right Arrow</td></tr>
<tr><td>Previous Track</td><td>[</td></tr>
<tr><td>Next Track</td><td>]</td></tr>
</table>
</dialog>
<button id="openKeyDialog">Keyboard Shortcuts</button>
</div>
<p style="text-align: center;">Maintained with help from <a href="https://github.com/fbecerra07"><b>fbecerra07 </b></a></p>
<?php 
//get a list of files stored in the playlist folder
$dir = 'media/playlist/';
$filenames = array_diff(scandir($dir), array('.', '..'));
$songs = $filenames;
shuffle($filenames);
?>

<script>

//variables from the page
var musicContainer = document.getElementById("music-container");
var playBtn = document.getElementById("play");
var prevBtn = document.getElementById("prev");
var nextBtn = document.getElementById("next");
var rwBTN = document.getElementById("rw");
var fwBTN = document.getElementById("fw");
var audio = document.getElementById('audio');
var title = document.getElementById('title');
var dialog = document.getElementById("shortcuts");
var showDialogButton = document.getElementById("openKeyDialog");
var hideDialogButton = document.getElementById("closeKeyDialog");


//grab the song titles from a php array
var songs = <?php echo json_encode($filenames); ?>;

// Keep track of song
let songIndex = 2;

// Initially load song details into DOM
loadSong(songs[songIndex]);

// Update song details
function loadSong(song) {
//split title and artist from filename string
  var name = song.split(".")[0].split("-- ")[0];
  var artist = song.split("-- ").pop();
//write title then artist below it
  title.innerText = name+"\n"+artist.split(".")[0];
//add audio to the audio tag
  audio.src = `media/playlist/${song}`;
}

// Play song
function playSong() {
  musicContainer.classList.add('play');
  playBtn.querySelector('i.fas').classList.remove('fa-play');
  playBtn.querySelector('i.fas').classList.add('fa-pause');
  audio.play();
  //playBTN.innerHTML = "Pause";
}

//play specific song
function playTrack(trackname) {
  loadSong(trackname);
  playSong();
}

// Pause song
function pauseSong() {
  musicContainer.classList.remove('play');
  playBtn.querySelector('i.fas').classList.add('fa-play');
  playBtn.querySelector('i.fas').classList.remove('fa-pause');
  audio.pause();
/*the below does not work, changing the html of the button somehow causes its event listener to stop functioning
  playBTN.innerHTML = "Play";*/
}

// Previous song
function prevSong() {
  songIndex--;
  if (songIndex < 0) {
    songIndex = songs.length - 1;
  }
  loadSong(songs[songIndex]);
  playSong();
}

// Next song
function nextSong() {
  songIndex++;
  if (songIndex > songs.length - 1) {
    songIndex = 0;
  }
  loadSong(songs[songIndex]);
  playSong();
}

//display progress of the track
function trackTime() {
  var progress = Math.floor(audio.currentTime / 60) + ":" + Math.round(audio.currentTime%60);
  var duration = Math.floor(audio.duration / 60) + ":" + Math.round(audio.duration%60);
  document.getElementById("progress").innerHTML = progress + "/" + duration;
}

//rewind function
function rewind() {
  var time = audio.currentTime;
  audio.currentTime = time-15;
}

//fastforward function
function fastforward() {
  var time = audio.currentTime;
  audio.currentTime = time+15;
}

//change volume
var volume = document.getElementById("volume");
volume.addEventListener("input", function (changeVolume) {
audio.volume = volume.value/100;
});

//play button
function playpause() {
var isPlaying = musicContainer.classList.contains('play');
if (isPlaying) {
    pauseSong();
  } else {
    playSong();
}
}

// Event listeners
playBtn.addEventListener('click', playpause);

// Change song
prevBtn.addEventListener('click', prevSong);
nextBtn.addEventListener('click', nextSong);
//show elapsed time
audio.addEventListener('timeupdate', trackTime);

// Song ends
audio.addEventListener('ended', nextSong);

//skip through a track
rwBTN.addEventListener("click", rewind);
fwBTN.addEventListener("click", fastforward);

//keyboard commands
document.addEventListener("keydown", function keyboard(event) {
  if (event.key === " ") {
    event.preventDefault();
    event.stopPropagation();
    playpause();
  }
  else if(event.key == "ArrowRight") {
    fastforward();
  }
  else if(event.key == "ArrowLeft") {
    rewind();
  }
  else if(event.key == "[") {
    prevSong();
  }
  else if(event.key == "]") {
    nextSong();
  }
});

//dialog events
showDialogButton.addEventListener("click", function show(){
dialog.show();
});
hideDialogButton.addEventListener("click", function hide(){
dialog.close()
});
//write the playlist to the page
document.getElementById("tracksarea").innerHTML += "<div id='songList'>\n<ul>";
var titles = <?php echo json_encode($songs); ?>;
for(var j = 2; j < Object.keys(titles).length; j++) {
document.getElementById("songList").innerHTML += "<li>"+titles[j].split(".")[0];
}
document.getElementById("songList").innerHTML += "</ul>";

</script>
</html>
