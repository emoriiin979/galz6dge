FROM python:3.12

WORKDIR /usr/src/app

COPY requirements.txt ./
RUN pip install --upgrade pip \
    && pip install --upgrade setuptools \
    && pip install --no-cache-dir -r requirements.txt
