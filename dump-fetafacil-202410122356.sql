PGDMP      8            	    |         	   fetafacil    17.0    17.0 2               0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false                       0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false                       0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false                       1262    16395 	   fetafacil    DATABASE     �   CREATE DATABASE fetafacil WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Portuguese_Luxembourg.1252';
    DROP DATABASE fetafacil;
                     postgres    false                        2615    2200    public    SCHEMA        CREATE SCHEMA public;
    DROP SCHEMA public;
                     pg_database_owner    false                       0    0    SCHEMA public    COMMENT     6   COMMENT ON SCHEMA public IS 'standard public schema';
                        pg_database_owner    false    4            �            1259    16452    cliente    TABLE     q   CREATE TABLE public.cliente (
    identificador character varying(500) NOT NULL,
    empresa boolean NOT NULL
);
    DROP TABLE public.cliente;
       public         heap r       postgres    false    4            �            1259    16445    configuracao    TABLE       CREATE TABLE public.configuracao (
    identificador character varying(500) NOT NULL,
    cliente_identificador character varying(500) NOT NULL,
    tempo_bloqueio integer NOT NULL,
    auto_pagamento_recebimento boolean NOT NULL,
    pin integer NOT NULL
);
     DROP TABLE public.configuracao;
       public         heap r       postgres    false    4            �            1259    16471 	   confirmar    TABLE       CREATE TABLE public.confirmar (
    cliente_identificador character varying,
    acao character varying NOT NULL,
    codigo_enviado character varying,
    quando character varying NOT NULL,
    confirmou boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.confirmar;
       public         heap r       postgres    false    4            �            1259    16485    confirmar_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.confirmar_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 2   DROP SEQUENCE public.confirmar_identificador_seq;
       public               postgres    false    4    228                       0    0    confirmar_identificador_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.confirmar_identificador_seq OWNED BY public.confirmar.identificador;
          public               postgres    false    230            �            1259    16438    contacto    TABLE       CREATE TABLE public.contacto (
    identificador character varying(500) NOT NULL,
    cliente_identificador character varying(500) NOT NULL,
    telefone character varying(500) NOT NULL,
    email character varying(500) NOT NULL,
    atual boolean NOT NULL
);
    DROP TABLE public.contacto;
       public         heap r       postgres    false    4            �            1259    16417    deposito    TABLE     �  CREATE TABLE public.deposito (
    transacao_pid character varying(500) NOT NULL,
    identificador character varying(500) NOT NULL,
    agente character varying(500) NOT NULL,
    notas json NOT NULL,
    total character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL
);
    DROP TABLE public.deposito;
       public         heap r       postgres    false    4            �            1259    16464    empresa    TABLE     ;  CREATE TABLE public.empresa (
    identificador character varying(500) NOT NULL,
    cliente_identificador character varying(500) NOT NULL,
    nif character varying(500) NOT NULL,
    nome character varying(500) NOT NULL,
    area_atuacao character varying(500) NOT NULL,
    balanco character varying NOT NULL
);
    DROP TABLE public.empresa;
       public         heap r       postgres    false    4            �            1259    16431    endereco    TABLE     -  CREATE TABLE public.endereco (
    identificador character varying(500) NOT NULL,
    provincia character varying(500) NOT NULL,
    cidade character varying(500) NOT NULL,
    bairro character varying(500) NOT NULL,
    cliente_identificador character varying NOT NULL,
    atual boolean NOT NULL
);
    DROP TABLE public.endereco;
       public         heap r       postgres    false    4            �            1259    16478    extrato    TABLE     �  CREATE TABLE public.extrato (
    identificador character varying(500) NOT NULL,
    identificador_conta character varying(500) NOT NULL,
    transacao_pid character varying(500) NOT NULL,
    entrada boolean NOT NULL,
    movimento character varying(500) NOT NULL,
    balanco character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL
);
    DROP TABLE public.extrato;
       public         heap r       postgres    false    4            �            1259    16424    levantamento    TABLE     �  CREATE TABLE public.levantamento (
    transacao_pid character varying(500) NOT NULL,
    identificador character varying(500) NOT NULL,
    agente character varying(500) NOT NULL,
    notas json NOT NULL,
    total character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL
);
     DROP TABLE public.levantamento;
       public         heap r       postgres    false    4            �            1259    16410 	   parcelado    TABLE     �  CREATE TABLE public.parcelado (
    transacao_pid character varying(500) NOT NULL,
    identificador character varying(500) NOT NULL,
    parcelas character varying(500) NOT NULL,
    valor_parcela character varying(500) NOT NULL,
    valor_total character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano bit varying(500) NOT NULL,
    estado boolean NOT NULL
);
    DROP TABLE public.parcelado;
       public         heap r       postgres    false    4            �            1259    16457 
   particular    TABLE     L  CREATE TABLE public.particular (
    identificador character varying(500) NOT NULL,
    cliente_identificador character varying(500) NOT NULL,
    bi character varying(500),
    nome character varying(500) NOT NULL,
    genero character varying(500),
    nascimento character varying(500),
    balanco character varying NOT NULL
);
    DROP TABLE public.particular;
       public         heap r       postgres    false    4            �            1259    16403 
   recorrente    TABLE     �  CREATE TABLE public.recorrente (
    transacao_pid character varying(500) NOT NULL,
    identificador character varying(500) NOT NULL,
    periodicidade character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    valor character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    estado boolean NOT NULL
);
    DROP TABLE public.recorrente;
       public         heap r       postgres    false    4            �            1259    16396 	   transacao    TABLE     �  CREATE TABLE public.transacao (
    pid character varying(500) NOT NULL,
    tipo character varying(500),
    de character varying(500),
    para character varying(500),
    onde character varying(500) NOT NULL,
    quando character varying(500),
    descricao character varying(500)[] NOT NULL,
    valor integer NOT NULL,
    dia character varying NOT NULL,
    mes character varying NOT NULL,
    ano character varying NOT NULL,
    executado boolean NOT NULL,
    identificador_conta character varying
);
    DROP TABLE public.transacao;
       public         heap r       postgres    false    4            Q           2604    16486    confirmar identificador    DEFAULT     �   ALTER TABLE ONLY public.confirmar ALTER COLUMN identificador SET DEFAULT nextval('public.confirmar_identificador_seq'::regclass);
 F   ALTER TABLE public.confirmar ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    230    228                      0    16452    cliente 
   TABLE DATA           9   COPY public.cliente (identificador, empresa) FROM stdin;
    public               postgres    false    225    D                 0    16445    configuracao 
   TABLE DATA           }   COPY public.configuracao (identificador, cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin) FROM stdin;
    public               postgres    false    224   OD                 0    16471 	   confirmar 
   TABLE DATA           r   COPY public.confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou, identificador) FROM stdin;
    public               postgres    false    228   lD                 0    16438    contacto 
   TABLE DATA           `   COPY public.contacto (identificador, cliente_identificador, telefone, email, atual) FROM stdin;
    public               postgres    false    223   �D                  0    16417    deposito 
   TABLE DATA           m   COPY public.deposito (transacao_pid, identificador, agente, notas, total, quando, dia, mes, ano) FROM stdin;
    public               postgres    false    220   �D                 0    16464    empresa 
   TABLE DATA           i   COPY public.empresa (identificador, cliente_identificador, nif, nome, area_atuacao, balanco) FROM stdin;
    public               postgres    false    227   �D                 0    16431    endereco 
   TABLE DATA           j   COPY public.endereco (identificador, provincia, cidade, bairro, cliente_identificador, atual) FROM stdin;
    public               postgres    false    222   E       	          0    16478    extrato 
   TABLE DATA           �   COPY public.extrato (identificador, identificador_conta, transacao_pid, entrada, movimento, balanco, quando, dia, mes, ano) FROM stdin;
    public               postgres    false    229   0E                 0    16424    levantamento 
   TABLE DATA           q   COPY public.levantamento (transacao_pid, identificador, agente, notas, total, quando, dia, mes, ano) FROM stdin;
    public               postgres    false    221   ME       �          0    16410 	   parcelado 
   TABLE DATA           �   COPY public.parcelado (transacao_pid, identificador, parcelas, valor_parcela, valor_total, quando, dia, mes, ano, estado) FROM stdin;
    public               postgres    false    219   jE                 0    16457 
   particular 
   TABLE DATA           q   COPY public.particular (identificador, cliente_identificador, bi, nome, genero, nascimento, balanco) FROM stdin;
    public               postgres    false    226   �E       �          0    16403 
   recorrente 
   TABLE DATA           w   COPY public.recorrente (transacao_pid, identificador, periodicidade, quando, valor, dia, mes, ano, estado) FROM stdin;
    public               postgres    false    218   �E       �          0    16396 	   transacao 
   TABLE DATA           �   COPY public.transacao (pid, tipo, de, para, onde, quando, descricao, valor, dia, mes, ano, executado, identificador_conta) FROM stdin;
    public               postgres    false    217   �E                  0    0    confirmar_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.confirmar_identificador_seq', 1, true);
          public               postgres    false    230            a           2606    16456    cliente cliente_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.cliente DROP CONSTRAINT cliente_pkey;
       public                 postgres    false    225            _           2606    16451    configuracao configuracao_pkey 
   CONSTRAINT     ~   ALTER TABLE ONLY public.configuracao
    ADD CONSTRAINT configuracao_pkey PRIMARY KEY (identificador, cliente_identificador);
 H   ALTER TABLE ONLY public.configuracao DROP CONSTRAINT configuracao_pkey;
       public                 postgres    false    224    224            g           2606    16493    confirmar confirmar_pk 
   CONSTRAINT     _   ALTER TABLE ONLY public.confirmar
    ADD CONSTRAINT confirmar_pk PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.confirmar DROP CONSTRAINT confirmar_pk;
       public                 postgres    false    228            ]           2606    16444    contacto contacto_pkey 
   CONSTRAINT     _   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_pkey PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_pkey;
       public                 postgres    false    223            Y           2606    16423    deposito deposito_pkey 
   CONSTRAINT     _   ALTER TABLE ONLY public.deposito
    ADD CONSTRAINT deposito_pkey PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.deposito DROP CONSTRAINT deposito_pkey;
       public                 postgres    false    220            e           2606    16470    empresa empresa_pkey 
   CONSTRAINT     t   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_pkey PRIMARY KEY (identificador, cliente_identificador);
 >   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_pkey;
       public                 postgres    false    227    227            i           2606    16484    extrato extrato_pkey 
   CONSTRAINT     l   ALTER TABLE ONLY public.extrato
    ADD CONSTRAINT extrato_pkey PRIMARY KEY (identificador, transacao_pid);
 >   ALTER TABLE ONLY public.extrato DROP CONSTRAINT extrato_pkey;
       public                 postgres    false    229    229            [           2606    16430    levantamento levantamento_pkey 
   CONSTRAINT     g   ALTER TABLE ONLY public.levantamento
    ADD CONSTRAINT levantamento_pkey PRIMARY KEY (identificador);
 H   ALTER TABLE ONLY public.levantamento DROP CONSTRAINT levantamento_pkey;
       public                 postgres    false    221            W           2606    16416    parcelado parcelado_pkey 
   CONSTRAINT     a   ALTER TABLE ONLY public.parcelado
    ADD CONSTRAINT parcelado_pkey PRIMARY KEY (identificador);
 B   ALTER TABLE ONLY public.parcelado DROP CONSTRAINT parcelado_pkey;
       public                 postgres    false    219            c           2606    16463    particular particular_pkey 
   CONSTRAINT     z   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT particular_pkey PRIMARY KEY (identificador, cliente_identificador);
 D   ALTER TABLE ONLY public.particular DROP CONSTRAINT particular_pkey;
       public                 postgres    false    226    226            U           2606    16409    recorrente recorrente_pkey 
   CONSTRAINT     c   ALTER TABLE ONLY public.recorrente
    ADD CONSTRAINT recorrente_pkey PRIMARY KEY (identificador);
 D   ALTER TABLE ONLY public.recorrente DROP CONSTRAINT recorrente_pkey;
       public                 postgres    false    218            S           2606    16532    transacao unique_transacao 
   CONSTRAINT     T   ALTER TABLE ONLY public.transacao
    ADD CONSTRAINT unique_transacao UNIQUE (pid);
 D   ALTER TABLE ONLY public.transacao DROP CONSTRAINT unique_transacao;
       public                 postgres    false    217            k           2606    16495    empresa fk_empresa    FK CONSTRAINT     �   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT fk_empresa FOREIGN KEY (cliente_identificador) REFERENCES public.cliente(identificador);
 <   ALTER TABLE ONLY public.empresa DROP CONSTRAINT fk_empresa;
       public               postgres    false    4705    227    225            j           2606    16526    particular fk_particular    FK CONSTRAINT     �   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT fk_particular FOREIGN KEY (cliente_identificador) REFERENCES public.cliente(identificador);
 B   ALTER TABLE ONLY public.particular DROP CONSTRAINT fk_particular;
       public               postgres    false    4705    226    225                  x�3�L�2�,�2b ���Ȋ���� C��            x������ � �         @   x��424�4732�LNLI,.)��4r9�tt��L��,�LL}9�8�b���� ��A            x������ � �             x������ � �            x������ � �            x������ � �      	      x������ � �            x������ � �      �      x������ � �            x������ � �      �      x������ � �      �      x������ � �     