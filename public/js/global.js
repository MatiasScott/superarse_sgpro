document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab-button");
  const sections = document.querySelectorAll(".form-section");
  const subTabs = document.querySelectorAll(".subtab-button");
  const subSections = document.querySelectorAll(".sub-section");

  function showSection(sectionId) {
    sections.forEach((section) => {
      section.classList.remove("active");
    });
    document.getElementById(sectionId).classList.add("active");
  }

  function showSubSection(subSectionId) {
    subSections.forEach((subSection) => {
      subSection.style.display = "none";
    });
    document.getElementById(subSectionId).style.display = "block";
  }

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");
      const sectionId = "section-" + tab.id.split("-")[1];
      showSection(sectionId);
    });
  });

  subTabs.forEach((subTab) => {
    subTab.addEventListener("click", () => {
      subTabs.forEach((st) => st.classList.remove("active"));
      subTab.classList.add("active");
      const subSectionId = "subsection-" + subTab.id.split("-")[1];
      showSubSection(subSectionId);
    });
  });
});
