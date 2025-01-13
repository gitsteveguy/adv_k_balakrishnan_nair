function togglemenu() {
  console.log("hello");

  offscreenmenu = document.querySelector(".off-screen-menu");
  offscreenmenu.classList.toggle("active");
}
const gallery_data = [
  {
    src: "prof_e_kunhiraman.jpg",
    member_name: "Prof. E. Kunhiraman",
    title: "Chief Patron",
    desc: "Adv. K. Balakrishnan Nair Foundation",
  },
  {
    src: "Adv_Dileep_Kumar.webp",
    member_name: "Adv. Dileep Kumar",
    title: "President",
    desc: "Bar Association Taliparamba",
  },
  {
    src: "Adv_O_V_Bindu.webp",
    member_name: "Adv BINDU O V",
    title: "Director",
    desc: "Adv. K. Balakrishnan Nair Foundation",
  },

  {
    src: "Steve_Sajan_Jacob.webp",
    member_name: "Steve Sajan Jacob",
    title: "Quiz Master & Tech Lead",
    desc: "Lexathon",
  },
];
gallery_grid = document.querySelector("#gallery_grid");
if (gallery_grid) {
  gallery_data.forEach((data) => {
    card = document.createElement("div");
    card.setAttribute("class", "card member-card");
    img = document.createElement("img");
    img.setAttribute("src", "./gallery/" + data.src);
    card.append(img);

    member_name = document.createElement("h3");
    member_name.innerText = data.member_name;
    card.append(member_name);

    title = document.createElement("h3");
    title.innerText = data.title;
    card.append(title);
    desc = document.createElement("p");
    desc.innerText = data.desc;
    card.append(desc);
    gallery_grid.append(card);
  });
}
