function togglemenu() {
  console.log("hello");

  offscreenmenu = document.querySelector(".off-screen-menu");
  offscreenmenu.classList.toggle("active");
}
const gallery_data = [
  {
    src: "prof_e_kunhiraman.jpg",
    title: "Chairman",
    desc: "COSTECH",
  },
  {
    src: "user.jpg",
    title: "Position",
    desc: "Organisation",
  },
  {
    src: "user.jpg",
    title: "Position",
    desc: "Organisation",
  },
  {
    src: "user.jpg",
    title: "Position",
    desc: "Organisation",
  },
  {
    src: "user.jpg",
    title: "Position",
    desc: "Organisation",
  },
  {
    src: "user.jpg",
    title: "Position",
    desc: "Organisation",
  },
  {
    src: "user.jpg",
    title: "Position",
    desc: "Organisation",
  },
];
gallery_grid = document.querySelector("#gallery_grid");
if (gallery_grid) {
  gallery_data.forEach((data) => {
    card = document.createElement("div");
    card.setAttribute("class", "card");
    img = document.createElement("img");
    img.setAttribute("src", "./gallery/" + data.src);
    card.append(img);
    title = document.createElement("h3");
    title.innerText = data.title;
    card.append(title);
    desc = document.createElement("p");
    desc.innerText = data.desc;
    card.append(desc);
    gallery_grid.append(card);
  });
}
