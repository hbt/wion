FROM ubuntu:14.04

RUN apt-get update && apt-get install curl rustc -y
RUN curl -sSf https://static.rust-lang.org/rustup.sh | sh
ADD . /wionc
RUN cd /wionc && cargo install

