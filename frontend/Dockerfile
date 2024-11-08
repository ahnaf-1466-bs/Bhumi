FROM node:18.19.0

RUN mkdir /frontend
WORKDIR /frontend

RUN npm install -g @angular/cli

COPY package.json package-lock.json ./

RUN npm install

COPY . .

RUN sed -i "s/#PLATFORM_DATA/platform_var/g" src/environments/environment_server.ts

# ARG frontend_port = 4244
RUN echo ""ng serve --host 0.0.0.0 --port 4244""
ENV PORT 4244

CMD ["sh","-c","ng serve --host 0.0.0.0 --port 4244"]
