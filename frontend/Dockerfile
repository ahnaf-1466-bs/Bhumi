FROM node:16.19.0

RUN mkdir /frontend
WORKDIR /frontend



RUN npm install -g @angular/cli

# RUN rm -rf src/environments/environment.ts
# RUN cp src/environments/environment_server.ts src/environments/environment.ts
RUN sed -i "s/#PLATFORM_DATA/platform_var/g" src/environments/environment.ts

COPY package.json package-lock.json ./

RUN npm install
ARG frontend_port
RUN echo ""ng serve --host 0.0.0.0 --port ${frontend_port}""
# RUN port=$((frontend_port))
# RUN echo ${port}
ENV PORT ${frontend_port}
COPY . .

CMD ["sh","-c","ng serve --host 0.0.0.0 --port ${PORT}"]
