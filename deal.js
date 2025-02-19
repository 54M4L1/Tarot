// MODULE 1: Declarations

//create new object with cards
var cards = new Array ();

//create variables to limit cards dealt to 7 & to test in console
var cardsLeftToDeal = 7;
var cardsDealt = 0;

// array containing the 78 cards with their img names
cards[00] = "00_Fool";
cards[01] = "01_Magician";
cards[02] = "02_High_Priestess";
cards[03] = "03_Empress";
cards[04] = "04_Emperor";
cards[05] = "05_Hierophant";
cards[06] = "06_Lovers";
cards[07] = "07_Chariot";
cards[08] = "08_Strength";
cards[09] = "09_Hermit";
cards[10] = "10_Wheel_of_Fortune";
cards[11] = "11_Justice";
cards[12] = "12_Hanged_Man";
cards[13] = "13_Death";
cards[14] = "14_Temperance";
cards[15] = "15_Devil";
cards[16] = "16_Tower";
cards[17] = "17_Star";
cards[18] = "18_Moon";
cards[19] = "19_Sun";
cards[20] = "20_Judgement";
cards[21] = "21_World";
cards[22] = "22_Ace_of_Wands";
cards[23] = "23_Two_of_Wands";
cards[24] = "24_Three_of_Wands";
cards[25] = "25_Four_of_Wands";
cards[26] = "26_Five_of_Wands";
cards[27] = "27_Six_of_Wands";
cards[28] = "28_Seven_of_Wands";
cards[29] = "29_Eight_of_Wands";
cards[30] = "30_Nine_of_Wands";
cards[31] = "31_Ten_of_Wands";
cards[32] = "32_Page_of_Wands";
cards[33] = "33_Knight_of_Wands";
cards[34] = "34_Queen_of_Wands";
cards[35] = "35_King_of_Wands";
cards[36] = "36_Ace_of_Pentacles";
cards[37] = "37_Two_of_Pentacles";
cards[38] = "38_Three_of_Pentacles";
cards[39] = "39_Four_of_Pentacles";
cards[40] = "40_Five_of_Pentacles";
cards[41] = "41_Six_of_Pentacles";
cards[42] = "42_Seven_of_Pentacles";
cards[43] = "43_Eight_of_Pentacles";
cards[44] = "44_Nine_of_Pentacles";
cards[45] = "45_Ten_of_Pentacles";
cards[46] = "46_Page_of_Pentacles";
cards[47] = "47_Knight_of_Pentacles";
cards[48] = "48_Queen_of_Pentacles";
cards[49] = "49_King_of_Pentacles";
cards[50] = "50_Ace_of_Cups";
cards[51] = "51_Two_of_Cups";
cards[52] = "52_Three_of_Cups";
cards[53] = "53_Four_of_Cups";
cards[54] = "54_Five_of_Cups";
cards[55] = "55_Six_of_Cups";
cards[56] = "56_Seven_of_Cups";
cards[57] = "57_Eight_of_Cups";
cards[58] = "58_Nine_of_Cups";
cards[59] = "59_Ten_of_Cups";
cards[60] = "60_Page_of_Cups";
cards[61] = "61_Knight_of_Cups";
cards[62] = "62_Queen_of_Cups";
cards[63] = "63_King_of_Cups";
cards[64] = "64_Ace_of_Swords";
cards[65] = "65_Two_of_Swords";
cards[66] = "66_Three_of_Swords";
cards[67] = "67_Four_of_Swords";
cards[68] = "68_Five_of_Swords";
cards[69] = "69_Six_of_Swords";
cards[70] = "70_Seven_of_Swords";
cards[71] = "71_Eight_of_Swords";
cards[72] = "72_Nine_of_Swords";
cards[73] = "73_Ten_of_Swords";
cards[74] = "74_Page_of_Swords";
cards[75] = "75_Knight_of_Swords";
cards[76] = "76_Queen_of_Swords";
cards[77] = "77_King_of_Swords";

// MODULE 2: Functions

// Function to randomly select a number that corresponds with the number of cards available
var random = function() {
  return Math.round(Math.random() * (77 - cardsDealt));
};

// Function to randomly determine if the card will be oriented up or down
var randomOrient = function() {
  return Math.round(Math.random());
};

// Function to remove a card once it has been used
var removeCard = function(k) {
  for (var j = k; j < cards.length; j++) {
    cards[j] = cards[j + 1];	
  }
  cardsLeftToDeal--;
  cardsDealt++;
};

// Function to deal the card and set its orientation
var dealCard = function(i) {
  if (cardsLeftToDeal == 0) {
    return false;
  } else {
    var img = document.createElement("img");
    var cardJustDealt = cards[i];
    var orient = randomOrient();
    $(img).addClass(cardJustDealt);
    img.src = ("/Tara/images/deck" + cards[i] + ".jpg");
    img.alt = cards[i];
    
    if (orient === 1) {
      $(img).addClass("orientationDown");
      document.getElementById("hand").appendChild(img);
      removeCard(i);
    } else {
      $(img).addClass("orientationUp");
      document.getElementById("hand").appendChild(img);
      removeCard(i);
    }
  }
};

// MODULE 3: Execution (JQuery)

$(document).ready(function() {
  // Deal a single card when deck is clicked
  $('#deal').click(function() {
    console.log(dealCard(random()));
  });

  // Shuffle the deck by reloading the page
  $('#shuffle').click(function() {
    location.reload();
  });

  // Deal 3 cards for simplified reading
  $('#simplified-reading').click(function() {
    for (let i = 0; i < 3; i++) {
      dealCard(random());
    }
  });

  // Deal 10 cards for detailed reading
  $('#detailed-reading').click(function() {
    for (let i = 0; i < 10; i++) {
      dealCard(random());
    }
  });
});
