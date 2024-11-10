# Step 1: Build the Angular app
FROM node:18.19.0 AS build

# Create app directory
WORKDIR /frontend

# Install Angular CLI
RUN npm install -g @angular/cli

# Copy package files and install dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy all files and replace variables
COPY . .
RUN sed -i "s/#PLATFORM_DATA/platform_var/g" src/environments/environment_server.ts

RUN ng build --configuration production --output-path=dist

# Step 2: Serve the app using Nginx
FROM nginx:alpine
COPY --from=build /frontend/dist /usr/share/nginx/html

# Expose the port
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
