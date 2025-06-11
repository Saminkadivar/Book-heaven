# Use official PHP image
FROM php:8.2-cli

# Set working directory
WORKDIR /app

# Copy everything into the container
COPY . .

# Expose port 10000 (Render requirement)
EXPOSE 10000

# Start the PHP server serving files from /app/public
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
