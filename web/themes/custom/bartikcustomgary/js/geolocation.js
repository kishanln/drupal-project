


async function getCoordinates(town) {
  console.log(town);
  const apiUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(town)}`;

  try {
      const response = await fetch(apiUrl);
      const data = await response.json();

      if (data && data.length > 0) {
          const latitude = data[0].lat;
          const longitude = data[0].lon;
          console.log(`${town}: Latitude ${latitude}, Longitude ${longitude}`);
      } else {
          console.error(`Coordinates not found for ${town}`);
      }
  } catch (error) {
      console.error(`Error fetching coordinates for ${town}: ${error}`);
  }
}

// Example usage
// getCoordinates('Sydney');
// getCoordinates('Subiaco');


