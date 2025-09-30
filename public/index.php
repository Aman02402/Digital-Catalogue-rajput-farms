<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rajput Farms Indoor Plant Catalogue</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Work+Sans:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body>
    <header class="hero">
      <div class="hero__overlay"></div>
      <div class="hero__content container">
        <div class="hero__badge">Since 1988</div>
        <h1>Rajput Farms Indoor Plant Catalogue</h1>
        <p>
          Discover more than two hundred meticulously curated indoor plants hand-selected for luxury homes,
          hotels, wellness retreats, and contemporary offices. Explore placement ideas, climate guidance, and
          styling notes for every botanical gem — now with gallery-ready imagery hosted directly by Rajput Farms.
        </p>
        <a class="hero__cta" href="#catalogue">Browse the collection</a>
      </div>
    </header>

    <main>
      <section class="intro container" aria-labelledby="intro-heading">
        <div>
          <h2 id="intro-heading">Why curate with Rajput Farms?</h2>
          <p>
            From air-purifying heroes to designer statement pieces, our indoor plant collections are assembled by
            horticulturists and stylists to suit India’s evolving interiors. Filter by care level, light, and climate to
            find the perfect match for your project or home.
          </p>
        </div>
        <dl class="intro__metrics" aria-label="Catalogue statistics">
          <div>
            <dt>Total curated varieties</dt>
            <dd id="metric-total">—</dd>
          </div>
          <div>
            <dt>Pet-friendly selections</dt>
            <dd id="metric-pet">—</dd>
          </div>
          <div>
            <dt>Air-wellness champions</dt>
            <dd id="metric-air">—</dd>
          </div>
        </dl>
      </section>

      <section id="catalogue" class="catalogue container" aria-labelledby="catalogue-heading">
        <div class="catalogue__header">
          <h2 id="catalogue-heading">Indoor Plant Collection</h2>
          <div class="filters" role="search">
            <label class="field" aria-label="Search catalogue">
              <span>Search</span>
              <input type="search" id="searchInput" placeholder="Search by name, use, or climate" />
            </label>
            <label class="field">
              <span>Care level</span>
              <select id="careFilter">
                <option value="">All</option>
                <option value="Very Easy">Very Easy</option>
                <option value="Easy">Easy</option>
                <option value="Moderate">Moderate</option>
                <option value="Challenging">Challenging</option>
              </select>
            </label>
            <label class="field">
              <span>Light keyword</span>
              <select id="lightFilter">
                <option value="">All</option>
                <option value="low light">Low light</option>
                <option value="medium">Medium light</option>
                <option value="bright">Bright light</option>
                <option value="direct sun">Direct sun</option>
              </select>
            </label>
            <label class="field">
              <span>Climate focus</span>
              <select id="climateFilter">
                <option value="">All</option>
                <option value="tropical">Tropical</option>
                <option value="humid">Humid</option>
                <option value="arid">Arid</option>
                <option value="temperate">Temperate</option>
                <option value="luxury">Luxury venues</option>
                <option value="corporate">Corporate interiors</option>
              </select>
            </label>
            <label class="toggle">
              <input type="checkbox" id="petFilter" />
              <span>Show only pet-friendly plants</span>
            </label>
          </div>
        </div>

        <div id="resultsMeta" class="catalogue__meta" aria-live="polite"></div>

        <div id="plantGrid" class="plant-grid" role="list"></div>
        <div class="catalogue__actions">
          <button id="loadMoreBtn" class="btn" type="button">Load more plants</button>
        </div>
      </section>

      <section class="submission" aria-labelledby="submission-heading">
        <div class="container submission__grid">
          <div>
            <h2 id="submission-heading">Add a bespoke plant to the catalogue</h2>
            <p>
              Partner nurseries and Rajput Farms stylists can extend the collection in real time. Submit a plant with
              placement recommendations, climate suitability, and care notes. Approved entries instantly appear in the
              live catalogue with hosted imagery.
            </p>
            <div class="submission__status" id="formStatus" role="status" aria-live="polite"></div>
          </div>

          <form id="plantForm" class="form" novalidate>
            <div class="form__row">
              <label class="field">
                <span>Common name *</span>
                <input type="text" name="commonName" required />
              </label>
              <label class="field">
                <span>Botanical name *</span>
                <input type="text" name="botanicalName" required />
              </label>
            </div>
            <label class="field">
              <span>Collection / range</span>
              <input type="text" name="collection" placeholder="e.g. Heritage Statement" />
            </label>
            <label class="field">
              <span>Ideal size or dimensions</span>
              <input type="text" name="size" placeholder="e.g. 4 ft sculptural specimen" />
            </label>
            <label class="field">
              <span>Highlight description *</span>
              <textarea name="description" rows="3" required placeholder="Describe the plant’s visual impact and story"></textarea>
            </label>
            <label class="field">
              <span>Primary uses (comma separated) *</span>
              <input type="text" name="uses" required placeholder="Air purification, foyer styling, gifting" />
            </label>
            <label class="field">
              <span>Placement ideas (comma separated) *</span>
              <input type="text" name="placementIdeas" required placeholder="Luxury lobby, reading nook, spa reception" />
            </label>
            <label class="field">
              <span>Environment notes *</span>
              <textarea name="environment" rows="2" required placeholder="Describe climate and room preferences"></textarea>
            </label>
            <label class="field">
              <span>Light requirements *</span>
              <input type="text" name="lightRequirements" required placeholder="Bright indirect light with gentle sun" />
            </label>
            <label class="field">
              <span>Watering schedule *</span>
              <input type="text" name="waterSchedule" required placeholder="Water when top 2 cm of soil are dry" />
            </label>
            <label class="field">
              <span>Humidity preference *</span>
              <input type="text" name="humidityPreference" required placeholder="Average to high humidity" />
            </label>
            <label class="field">
              <span>Temperature range *</span>
              <input type="text" name="temperatureRange" required placeholder="18-26°C" />
            </label>
            <label class="field">
              <span>Care level *</span>
              <select name="careLevel" required>
                <option value="">Select</option>
                <option value="Very Easy">Very Easy</option>
                <option value="Easy">Easy</option>
                <option value="Moderate">Moderate</option>
                <option value="Challenging">Challenging</option>
              </select>
            </label>
            <label class="toggle">
              <input type="checkbox" name="petFriendly" />
              <span>Pet-friendly selection</span>
            </label>
            <label class="field">
              <span>Climate focus (comma separated)</span>
              <input type="text" name="climateFocus" placeholder="Tropical resorts, air-conditioned offices" />
            </label>
            <label class="field">
              <span>Feature tags (comma separated)</span>
              <input type="text" name="featuredTags" placeholder="Statement, Air wellness" />
            </label>
            <label class="field">
              <span>Special notes</span>
              <textarea name="specialNotes" rows="2" placeholder="Mention packaging, planter, or styling tip"></textarea>
            </label>
            <button class="btn" type="submit">Submit plant for listing</button>
          </form>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div class="container footer__grid">
        <div>
          <h3>About Rajput Farms</h3>
          <p>
            Rajput Farms curates luxury landscaping and indoor botanical styling across India, UAE, and Southeast Asia.
            Our catalogue evolves with design trends, climate insights, and feedback from architects and interior
            partners.
          </p>
        </div>
        <div>
          <h3>Connect</h3>
          <ul>
            <li><a href="mailto:hello@rajputfarms.in">hello@rajputfarms.in</a></li>
            <li><a href="tel:+919876543210">+91 98765 43210</a></li>
            <li><a href="#catalogue">Book a consultation</a></li>
          </ul>
        </div>
        <div>
          <h3>Visit</h3>
          <p>
            Rajput Farms Experience Centre<br />
            NH-48, Near Manesar<br />
            Gurugram, Haryana
          </p>
        </div>
      </div>
      <p class="footer__note">© <span id="year"></span> Rajput Farms. Crafted with living design.</p>
    </footer>

    <script src="/app.js" type="module"></script>
  </body>
</html>
