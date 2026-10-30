/* =================================
   Practical 6: Fetch API
   Events JSON
================================= */

const p6EventSearch = document.getElementById("eventSearch");
const p6EventFilter = document.getElementById("eventFilter");
const p6EventSort = document.getElementById("eventSort");

const p6EventsContainer = document.getElementById("eventsContainer");
const p6EventsStatus = document.getElementById("eventsStatus");
const p6EventPagination = document.getElementById("eventPagination");

const p6EventSlider = document.getElementById("eventSlider");

let p6Events = [];
let p6FilteredEvents = [];
let p6CurrentPage = 1;

const p6EventsPerPage = 3;
let p6CurrentSlide = 0;


/* Fetch Events */

async function loadP6Events() {

    p6EventsStatus.textContent = "Loading events...";

    try {

        const response = await fetch("../data/events-test.json");

        if (!response.ok) {
            throw new Error("Unable to load events.");
        }

        p6Events = await response.json();

        // Keep loading state visible briefly
        await new Promise(resolve => setTimeout(resolve, 1000));

        p6FilteredEvents = [...p6Events];

        createP6CategoryOptions();

        applyP6Filters();

        p6EventsStatus.textContent =
            `${p6Events.length} events loaded successfully.`;

    } catch (error) {

        p6EventsStatus.textContent =
            "Unable to load events. Please try again later.";

        console.error(error);
    }
}


/* Create Filter Options */

function createP6CategoryOptions() {

    const categories = [...new Set(
        p6Events.map(event => event.category)
    )];

    categories.forEach(category => {

        const option = document.createElement("option");

        option.value = category;
        option.textContent = category;

        p6EventFilter.appendChild(option);
    });
}


/* Search, Filter and Sort */

function applyP6Filters() {

    const searchText =
        p6EventSearch.value.toLowerCase().trim();

    const selectedCategory =
        p6EventFilter.value;

    p6FilteredEvents = p6Events.filter(event => {

        const matchesSearch =
            event.title.toLowerCase().includes(searchText) ||
            event.description.toLowerCase().includes(searchText) ||
            event.category.toLowerCase().includes(searchText);

        const matchesCategory =
            selectedCategory === "all" ||
            event.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    sortP6Events();

    p6CurrentPage = 1;
    p6CurrentSlide = 0;

    renderP6Events();
    renderP6FeaturedEvents();
    renderP6Pagination();
}


/* Sorting */

function sortP6Events() {

    const sortValue = p6EventSort.value;

    p6FilteredEvents.sort((a, b) => {

        if (sortValue === "date-asc") {
            return new Date(a.date) - new Date(b.date);
        }

        if (sortValue === "date-desc") {
            return new Date(b.date) - new Date(a.date);
        }

        if (sortValue === "title-asc") {
            return a.title.localeCompare(b.title);
        }

        if (sortValue === "title-desc") {
            return b.title.localeCompare(a.title);
        }

    });
}


/* Render Events */

function renderP6Events() {

    p6EventsContainer.innerHTML = "";

    if (p6FilteredEvents.length === 0) {

        p6EventsContainer.innerHTML =
            "<p>No events found.</p>";

        return;
    }

    const start =
        (p6CurrentPage - 1) * p6EventsPerPage;

    const end =
        start + p6EventsPerPage;

    const pageEvents =
        p6FilteredEvents.slice(start, end);


    pageEvents.forEach(event => {

        const article =
            document.createElement("article");

        article.className = "dynamic-event-card";

        article.innerHTML = `
            <p>
                ${formatP6Date(event.date)}
                • ${event.category}
                • ${event.venue}
            </p>

            <h4>${event.title}</h4>

            <p>${event.description}</p>

            <p>
                <strong>Venue:</strong> ${event.venue}
            </p>
        `;

        p6EventsContainer.appendChild(article);
    });
}


/* Featured Event Slider */

function renderP6FeaturedEvents() {

    const featuredEvents =
        p6FilteredEvents.slice(0, 3);

    p6EventSlider.innerHTML = "";

    if (featuredEvents.length === 0) {

        p6EventSlider.innerHTML =
            "<p>No featured events available.</p>";

        return;
    }


    featuredEvents.forEach((event, index) => {

        const article =
            document.createElement("article");

        article.className =
            index === p6CurrentSlide
                ? "event-slide active"
                : "event-slide";

        article.innerHTML = `
            <p>${event.category}</p>

            <p>${formatP6Date(event.date)}</p>

            <h3>${event.title}</h3>

            <p>${event.description}</p>

            <p>${event.venue}</p>

            <a href="#">View Details</a>
        `;

        p6EventSlider.appendChild(article);
    });


    const buttons =
        document.createElement("div");

    buttons.className = "slider-buttons";

    buttons.innerHTML = `
        <button
            type="button"
            id="prevEvent"
            aria-label="Previous event">
            ← Previous
        </button>

        <button
            type="button"
            id="nextEvent"
            aria-label="Next event">
            Next →
        </button>
    `;

    p6EventSlider.appendChild(buttons);


    document.getElementById("prevEvent")
        .addEventListener("click", showP6PreviousEvent);

    document.getElementById("nextEvent")
        .addEventListener("click", showP6NextEvent);
}


/* Slider Controls */

function showP6NextEvent() {

    const slides =
        document.querySelectorAll(".event-slide");

    if (slides.length === 0) return;

    p6CurrentSlide =
        (p6CurrentSlide + 1) % slides.length;

    updateP6Slider(slides);
}


function showP6PreviousEvent() {

    const slides =
        document.querySelectorAll(".event-slide");

    if (slides.length === 0) return;

    p6CurrentSlide =
        (p6CurrentSlide - 1 + slides.length) %
        slides.length;

    updateP6Slider(slides);
}


function updateP6Slider(slides) {

    slides.forEach((slide, index) => {

        slide.classList.toggle(
            "active",
            index === p6CurrentSlide
        );
    });
}


/* Pagination */

function renderP6Pagination() {

    p6EventPagination.innerHTML = "";

    const totalPages =
        Math.ceil(
            p6FilteredEvents.length / p6EventsPerPage
        );

    if (totalPages <= 1) return;


    for (let page = 1; page <= totalPages; page++) {

        const button =
            document.createElement("button");

        button.type = "button";
        button.textContent = page;

        if (page === p6CurrentPage) {
            button.classList.add("active");
        }

        button.addEventListener("click", function () {

            p6CurrentPage = page;

            renderP6Events();
            renderP6Pagination();

        });

        p6EventPagination.appendChild(button);
    }
}


/* Date Formatting */

function formatP6Date(date) {

    return new Date(date).toLocaleDateString(
        "en-IN",
        {
            day: "2-digit",
            month: "short",
            year: "numeric"
        }
    );
}


/* Event Listeners */
if (p6EventSearch && p6EventFilter && p6EventSort) {
    p6EventSearch.addEventListener("input", applyP6Filters);
    p6EventFilter.addEventListener("change", applyP6Filters);
    p6EventSort.addEventListener("change", applyP6Filters);

    /* Start */
    loadP6Events();
}

/* =================================
   Practical 6: Students
================================= */

const p6StudentSearch =
    document.getElementById("studentSearch");

const p6StudentsContainer =
    document.getElementById("studentsContainer");

const p6StudentsStatus =
    document.getElementById("studentsStatus");

let p6Students = [];


/* Fetch Students */

async function loadP6Students() {

    p6StudentsStatus.textContent =
        "Loading students...";

    try {

        const response =
            await fetch("../data/students.json");

        if (!response.ok) {
            throw new Error("Unable to load students.");
        }

        p6Students = await response.json();

        await new Promise(resolve =>
            setTimeout(resolve, 1000)
        );

        renderP6Students(p6Students);

        p6StudentsStatus.textContent =
            `${p6Students.length} students loaded successfully.`;

    } catch (error) {

        p6StudentsStatus.textContent =
            "Unable to load students. Please try again later.";

        console.error(error);
    }
}


/* Render Students */

function renderP6Students(students) {

    p6StudentsContainer.innerHTML = "";

    if (students.length === 0) {

        p6StudentsContainer.innerHTML =
            "<p>No students found.</p>";

        return;
    }

    students.forEach(student => {

        const article =
            document.createElement("article");

        article.className =
            "dynamic-student-card";

        article.innerHTML = `
            <h4>${student.name}</h4>

            <p>
                <strong>ID:</strong>
                ${student.id}
            </p>

            <p>
                <strong>Email:</strong>
                ${student.email}
            </p>

            <p>
                <strong>Course:</strong>
                ${student.course}
            </p>

            <p>
                <strong>Year:</strong>
                ${student.year}
            </p>

            <p>
                <strong>Department:</strong>
                ${student.department}
            </p>
        `;

        p6StudentsContainer.appendChild(article);
    });
}


/* Search Students */
if (p6StudentSearch && p6StudentsContainer && p6StudentsStatus) {
    p6StudentSearch.addEventListener(
        "input",
        function () {
            const searchText =
                p6StudentSearch.value.toLowerCase().trim();

            const filteredStudents =
                p6Students.filter(student =>
                    student.name.toLowerCase().includes(searchText) ||
                    student.email.toLowerCase().includes(searchText) ||
                    student.course.toLowerCase().includes(searchText) ||
                    student.department.toLowerCase().includes(searchText)
                );

            renderP6Students(filteredStudents);
        }
    );

    /* Start */
    loadP6Students();
}


/* =================================
   Practical 6: FAQs
================================= */

// Select HTML Elements
const p6FaqSearch = document.getElementById("faqSearch");
const p6FaqFilter = document.getElementById("faqFilter");
const p6FaqSort = document.getElementById("faqSort");
const p6FaqsContainer = document.getElementById("faqsContainer");
const p6FaqsStatus = document.getElementById("faqsStatus");
const p6FaqPagination = document.getElementById("faqPagination");

let p6Faqs = [];
let p6FilteredFaqs = [];
let p6FaqCurrentPage = 1;
const p6FaqsPerPage = 5;

// Fetch FAQ Data
async function loadP6Faqs() {
    p6FaqsStatus.textContent = "Loading FAQs...";

    try {
        const response = await fetch("../data/faqs.json");

        if (!response.ok) {
            throw new Error("Unable to load FAQs.");
        }

        p6Faqs = await response.json();

        await new Promise(resolve => setTimeout(resolve, 1000));

        createP6FaqCategories();
        applyP6FaqFilters();

        p6FaqsStatus.textContent =
            `${p6Faqs.length} FAQs loaded successfully.`;

    } catch (error) {
        p6FaqsStatus.textContent =
            "Unable to load FAQs. Please try again later.";

        p6FaqsContainer.innerHTML = "";
        p6FaqPagination.innerHTML = "";

        console.error(error);
    }
}

// Create Category Filter Options
function createP6FaqCategories() {
    const categories = [
        ...new Set(p6Faqs.map(faq => faq.category))
    ];

    p6FaqFilter.innerHTML =
        '<option value="all">All Categories</option>';

    categories.forEach(category => {
        const option = document.createElement("option");
        option.value = category;
        option.textContent = category;
        p6FaqFilter.appendChild(option);
    });
}

// Search and Filter FAQs
function applyP6FaqFilters() {
    const searchText = p6FaqSearch.value.toLowerCase().trim();
    const selectedCategory = p6FaqFilter.value;

    p6FilteredFaqs = p6Faqs.filter(faq => {
        const matchesSearch =
            faq.question.toLowerCase().includes(searchText) ||
            faq.answer.toLowerCase().includes(searchText);

        const matchesCategory =
            selectedCategory === "all" ||
            faq.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    sortP6Faqs();
}

// Sort FAQs
function sortP6Faqs() {
    const sortValue = p6FaqSort.value;

    p6FilteredFaqs.sort((a, b) => {
        if (sortValue === "az") {
            return a.question.localeCompare(b.question);
        }

        if (sortValue === "za") {
            return b.question.localeCompare(a.question);
        }

        return a.id - b.id;
    });

    p6FaqCurrentPage = 1;
    renderP6Faqs();
}

// Display FAQs Dynamically
function renderP6Faqs() {
    p6FaqsContainer.innerHTML = "";
    p6FaqPagination.innerHTML = "";

    if (p6FilteredFaqs.length === 0) {
        p6FaqsContainer.innerHTML = "<p>No FAQs found.</p>";
        return;
    }

    const startIndex = (p6FaqCurrentPage - 1) * p6FaqsPerPage;
    const currentFaqs = p6FilteredFaqs.slice(
        startIndex,
        startIndex + p6FaqsPerPage
    );

    currentFaqs.forEach(faq => {
        const article = document.createElement("article");
        article.className = "dynamic-faq-card";

        const question = document.createElement("h3");
        question.textContent = faq.question;

        const category = document.createElement("p");
        category.className = "faq-category";
        category.textContent = `Category: ${faq.category}`;

        const answer = document.createElement("p");
        answer.textContent = faq.answer;

        article.append(question, category, answer);
        p6FaqsContainer.appendChild(article);
    });

    renderP6FaqPagination();
}

// Pagination
function renderP6FaqPagination() {
    const totalPages = Math.ceil(
        p6FilteredFaqs.length / p6FaqsPerPage
    );

    if (totalPages <= 1) return;

    for (let page = 1; page <= totalPages; page++) {
        const button = document.createElement("button");
        button.textContent = page;
        button.className =
            page === p6FaqCurrentPage ? "active" : "";

        button.addEventListener("click", () => {
            p6FaqCurrentPage = page;
            renderP6Faqs();
        });

        p6FaqPagination.appendChild(button);
    }
}

// Run FAQ Features Only on the FAQ Page
if (
    p6FaqSearch &&
    p6FaqFilter &&
    p6FaqSort &&
    p6FaqsContainer &&
    p6FaqsStatus &&
    p6FaqPagination
) {
    p6FaqSearch.addEventListener("input", applyP6FaqFilters);
    p6FaqFilter.addEventListener("change", applyP6FaqFilters);
    p6FaqSort.addEventListener("change", sortP6Faqs);

    loadP6Faqs();
}

