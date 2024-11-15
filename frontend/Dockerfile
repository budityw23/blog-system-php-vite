# Build stage
FROM node:16-alpine AS build-stage
WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm install

# Copy project files
COPY . .

# Build the project
RUN npm run build

# Production stage
FROM nginx:stable-alpine AS production-stage

# Copy built files
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Copy nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
