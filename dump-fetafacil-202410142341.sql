PGDMP      )            	    |         	   fetafacil    17.0    17.0 N    '           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            (           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            )           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            *           1262    16395 	   fetafacil    DATABASE     �   CREATE DATABASE fetafacil WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Portuguese_Luxembourg.1252';
    DROP DATABASE fetafacil;
                     postgres    false                        2615    2200    public    SCHEMA        CREATE SCHEMA public;
    DROP SCHEMA public;
                     pg_database_owner    false            +           0    0    SCHEMA public    COMMENT     6   COMMENT ON SCHEMA public IS 'standard public schema';
                        pg_database_owner    false    4            �            1259    16452    cliente    TABLE     q   CREATE TABLE public.cliente (
    identificador character varying(500) NOT NULL,
    empresa boolean NOT NULL
);
    DROP TABLE public.cliente;
       public         heap r       postgres    false    4            �            1259    16445    configuracao    TABLE     �   CREATE TABLE public.configuracao (
    cliente_identificador character varying(500) NOT NULL,
    tempo_bloqueio integer NOT NULL,
    auto_pagamento_recebimento boolean NOT NULL,
    pin text NOT NULL,
    identificador bigint NOT NULL
);
     DROP TABLE public.configuracao;
       public         heap r       postgres    false    4            �            1259    16622    configuracao_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.configuracao_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 5   DROP SEQUENCE public.configuracao_identificador_seq;
       public               postgres    false    224    4            ,           0    0    configuracao_identificador_seq    SEQUENCE OWNED BY     a   ALTER SEQUENCE public.configuracao_identificador_seq OWNED BY public.configuracao.identificador;
          public               postgres    false    234            �            1259    16471 	   confirmar    TABLE       CREATE TABLE public.confirmar (
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
       public               postgres    false    4    228            -           0    0    confirmar_identificador_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.confirmar_identificador_seq OWNED BY public.confirmar.identificador;
          public               postgres    false    230            �            1259    16438    contacto    TABLE     �   CREATE TABLE public.contacto (
    cliente_identificador character varying(500) NOT NULL,
    telefone character varying(500) NOT NULL,
    email character varying(500),
    atual boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.contacto;
       public         heap r       postgres    false    4            �            1259    16631    contacto_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.contacto_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 1   DROP SEQUENCE public.contacto_identificador_seq;
       public               postgres    false    223    4            .           0    0    contacto_identificador_seq    SEQUENCE OWNED BY     Y   ALTER SEQUENCE public.contacto_identificador_seq OWNED BY public.contacto.identificador;
          public               postgres    false    235            �            1259    16417    deposito    TABLE     b  CREATE TABLE public.deposito (
    transacao_pid character varying(500) NOT NULL,
    agente character varying(500) NOT NULL,
    notas json NOT NULL,
    total numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL
);
    DROP TABLE public.deposito;
       public         heap r       postgres    false    4            �            1259    16464    empresa    TABLE     .  CREATE TABLE public.empresa (
    identificador character varying(500) NOT NULL,
    cliente_identificador character varying(500) NOT NULL,
    nif character varying(500) NOT NULL,
    nome character varying(500) NOT NULL,
    area_atuacao character varying(500) NOT NULL,
    balanco numeric(16,2)
);
    DROP TABLE public.empresa;
       public         heap r       postgres    false    4            �            1259    16431    endereco    TABLE       CREATE TABLE public.endereco (
    provincia character varying(500),
    cidade character varying(500),
    bairro character varying(500),
    cliente_identificador character varying NOT NULL,
    atual boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.endereco;
       public         heap r       postgres    false    4            �            1259    16640    endereco_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.endereco_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 1   DROP SEQUENCE public.endereco_identificador_seq;
       public               postgres    false    4    222            /           0    0    endereco_identificador_seq    SEQUENCE OWNED BY     Y   ALTER SEQUENCE public.endereco_identificador_seq OWNED BY public.endereco.identificador;
          public               postgres    false    236            �            1259    16478    extrato    TABLE     �  CREATE TABLE public.extrato (
    identificador_conta character varying(500) NOT NULL,
    transacao_pid character varying(500) NOT NULL,
    entrada boolean NOT NULL,
    movimento numeric(16,2) NOT NULL,
    balanco numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.extrato;
       public         heap r       postgres    false    4            �            1259    16533    extrato_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.extrato_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 0   DROP SEQUENCE public.extrato_identificador_seq;
       public               postgres    false    4    229            0           0    0    extrato_identificador_seq    SEQUENCE OWNED BY     W   ALTER SEQUENCE public.extrato_identificador_seq OWNED BY public.extrato.identificador;
          public               postgres    false    231            �            1259    16424    levantamento    TABLE     �  CREATE TABLE public.levantamento (
    transacao_pid character varying(500) NOT NULL,
    agente character varying(500) NOT NULL,
    notas json NOT NULL,
    total numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    identificador bigint NOT NULL
);
     DROP TABLE public.levantamento;
       public         heap r       postgres    false    4            �            1259    16542    levantamento_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.levantamento_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 5   DROP SEQUENCE public.levantamento_identificador_seq;
       public               postgres    false    221    4            1           0    0    levantamento_identificador_seq    SEQUENCE OWNED BY     a   ALTER SEQUENCE public.levantamento_identificador_seq OWNED BY public.levantamento.identificador;
          public               postgres    false    232            �            1259    16410 	   parcelado    TABLE     �  CREATE TABLE public.parcelado (
    transacao_pid character varying(500) NOT NULL,
    parcelas character varying(500) NOT NULL,
    valor_parcela numeric(16,2) NOT NULL,
    valor_total numeric(16,2) NOT NULL,
    quando character varying(500) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano bit varying(500) NOT NULL,
    estado boolean NOT NULL,
    identificador bigint NOT NULL
);
    DROP TABLE public.parcelado;
       public         heap r       postgres    false    4            �            1259    16551    parcelado_identificador_seq    SEQUENCE     �   CREATE SEQUENCE public.parcelado_identificador_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 2   DROP SEQUENCE public.parcelado_identificador_seq;
       public               postgres    false    219    4            2           0    0    parcelado_identificador_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.parcelado_identificador_seq OWNED BY public.parcelado.identificador;
          public               postgres    false    233            �            1259    16457 
   particular    TABLE     C  CREATE TABLE public.particular (
    cliente_identificador character varying(500) NOT NULL,
    bi character varying(500),
    nome character varying(500) NOT NULL,
    genero character varying(500),
    nascimento character varying(500),
    balanco numeric(16,2) NOT NULL,
    identificador character varying NOT NULL
);
    DROP TABLE public.particular;
       public         heap r       postgres    false    4            �            1259    16403 
   recorrente    TABLE     o  CREATE TABLE public.recorrente (
    transacao_pid character varying(500) NOT NULL,
    periodicidade character varying(500) NOT NULL,
    quando character varying(500) NOT NULL,
    valor numeric(16,2) NOT NULL,
    dia character varying(500) NOT NULL,
    mes character varying(500) NOT NULL,
    ano character varying(500) NOT NULL,
    estado boolean NOT NULL
);
    DROP TABLE public.recorrente;
       public         heap r       postgres    false    4            �            1259    16396 	   transacao    TABLE       CREATE TABLE public.transacao (
    pid character varying(500) NOT NULL,
    tipo character varying(500),
    de character varying(500),
    para character varying(500),
    onde character varying(500) NOT NULL,
    quando character varying(500),
    descricao character varying(500)[] NOT NULL,
    valor numeric(16,2) NOT NULL,
    dia character varying NOT NULL,
    mes character varying NOT NULL,
    ano character varying NOT NULL,
    executado boolean NOT NULL,
    identificador_conta character varying
);
    DROP TABLE public.transacao;
       public         heap r       postgres    false    4            [           2604    16623    configuracao identificador    DEFAULT     �   ALTER TABLE ONLY public.configuracao ALTER COLUMN identificador SET DEFAULT nextval('public.configuracao_identificador_seq'::regclass);
 I   ALTER TABLE public.configuracao ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    234    224            \           2604    16486    confirmar identificador    DEFAULT     �   ALTER TABLE ONLY public.confirmar ALTER COLUMN identificador SET DEFAULT nextval('public.confirmar_identificador_seq'::regclass);
 F   ALTER TABLE public.confirmar ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    230    228            Z           2604    16632    contacto identificador    DEFAULT     �   ALTER TABLE ONLY public.contacto ALTER COLUMN identificador SET DEFAULT nextval('public.contacto_identificador_seq'::regclass);
 E   ALTER TABLE public.contacto ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    235    223            Y           2604    16641    endereco identificador    DEFAULT     �   ALTER TABLE ONLY public.endereco ALTER COLUMN identificador SET DEFAULT nextval('public.endereco_identificador_seq'::regclass);
 E   ALTER TABLE public.endereco ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    236    222            ]           2604    16534    extrato identificador    DEFAULT     ~   ALTER TABLE ONLY public.extrato ALTER COLUMN identificador SET DEFAULT nextval('public.extrato_identificador_seq'::regclass);
 D   ALTER TABLE public.extrato ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    231    229            X           2604    16543    levantamento identificador    DEFAULT     �   ALTER TABLE ONLY public.levantamento ALTER COLUMN identificador SET DEFAULT nextval('public.levantamento_identificador_seq'::regclass);
 I   ALTER TABLE public.levantamento ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    232    221            W           2604    16552    parcelado identificador    DEFAULT     �   ALTER TABLE ONLY public.parcelado ALTER COLUMN identificador SET DEFAULT nextval('public.parcelado_identificador_seq'::regclass);
 F   ALTER TABLE public.parcelado ALTER COLUMN identificador DROP DEFAULT;
       public               postgres    false    233    219                      0    16452    cliente 
   TABLE DATA           9   COPY public.cliente (identificador, empresa) FROM stdin;
    public               postgres    false    225   �c                 0    16445    configuracao 
   TABLE DATA           }   COPY public.configuracao (cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin, identificador) FROM stdin;
    public               postgres    false    224   �c                 0    16471 	   confirmar 
   TABLE DATA           r   COPY public.confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou, identificador) FROM stdin;
    public               postgres    false    228   d                 0    16438    contacto 
   TABLE DATA           `   COPY public.contacto (cliente_identificador, telefone, email, atual, identificador) FROM stdin;
    public               postgres    false    223   Wd                 0    16417    deposito 
   TABLE DATA           ^   COPY public.deposito (transacao_pid, agente, notas, total, quando, dia, mes, ano) FROM stdin;
    public               postgres    false    220   �d                 0    16464    empresa 
   TABLE DATA           i   COPY public.empresa (identificador, cliente_identificador, nif, nome, area_atuacao, balanco) FROM stdin;
    public               postgres    false    227   �d                 0    16431    endereco 
   TABLE DATA           j   COPY public.endereco (provincia, cidade, bairro, cliente_identificador, atual, identificador) FROM stdin;
    public               postgres    false    222   �d                 0    16478    extrato 
   TABLE DATA           �   COPY public.extrato (identificador_conta, transacao_pid, entrada, movimento, balanco, quando, dia, mes, ano, identificador) FROM stdin;
    public               postgres    false    229   �d                 0    16424    levantamento 
   TABLE DATA           q   COPY public.levantamento (transacao_pid, agente, notas, total, quando, dia, mes, ano, identificador) FROM stdin;
    public               postgres    false    221   e                 0    16410 	   parcelado 
   TABLE DATA           �   COPY public.parcelado (transacao_pid, parcelas, valor_parcela, valor_total, quando, dia, mes, ano, estado, identificador) FROM stdin;
    public               postgres    false    219   %e                 0    16457 
   particular 
   TABLE DATA           q   COPY public.particular (cliente_identificador, bi, nome, genero, nascimento, balanco, identificador) FROM stdin;
    public               postgres    false    226   Be                 0    16403 
   recorrente 
   TABLE DATA           h   COPY public.recorrente (transacao_pid, periodicidade, quando, valor, dia, mes, ano, estado) FROM stdin;
    public               postgres    false    218   _e                 0    16396 	   transacao 
   TABLE DATA           �   COPY public.transacao (pid, tipo, de, para, onde, quando, descricao, valor, dia, mes, ano, executado, identificador_conta) FROM stdin;
    public               postgres    false    217   |e       3           0    0    configuracao_identificador_seq    SEQUENCE SET     L   SELECT pg_catalog.setval('public.configuracao_identificador_seq', 6, true);
          public               postgres    false    234            4           0    0    confirmar_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.confirmar_identificador_seq', 1, true);
          public               postgres    false    230            5           0    0    contacto_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.contacto_identificador_seq', 14, true);
          public               postgres    false    235            6           0    0    endereco_identificador_seq    SEQUENCE SET     I   SELECT pg_catalog.setval('public.endereco_identificador_seq', 14, true);
          public               postgres    false    236            7           0    0    extrato_identificador_seq    SEQUENCE SET     H   SELECT pg_catalog.setval('public.extrato_identificador_seq', 1, false);
          public               postgres    false    231            8           0    0    levantamento_identificador_seq    SEQUENCE SET     M   SELECT pg_catalog.setval('public.levantamento_identificador_seq', 1, false);
          public               postgres    false    232            9           0    0    parcelado_identificador_seq    SEQUENCE SET     J   SELECT pg_catalog.setval('public.parcelado_identificador_seq', 1, false);
          public               postgres    false    233            o           2606    16456    cliente cliente_pkey 
   CONSTRAINT     ]   ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.cliente DROP CONSTRAINT cliente_pkey;
       public                 postgres    false    225            m           2606    16630    configuracao configuracao_pk 
   CONSTRAINT     e   ALTER TABLE ONLY public.configuracao
    ADD CONSTRAINT configuracao_pk PRIMARY KEY (identificador);
 F   ALTER TABLE ONLY public.configuracao DROP CONSTRAINT configuracao_pk;
       public                 postgres    false    224            {           2606    16493    confirmar confirmar_pk 
   CONSTRAINT     _   ALTER TABLE ONLY public.confirmar
    ADD CONSTRAINT confirmar_pk PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.confirmar DROP CONSTRAINT confirmar_pk;
       public                 postgres    false    228            g           2606    16660    contacto contacto_email_key 
   CONSTRAINT     W   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_email_key UNIQUE (email);
 E   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_email_key;
       public                 postgres    false    223            i           2606    16639    contacto contacto_pk 
   CONSTRAINT     ]   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_pk PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_pk;
       public                 postgres    false    223            k           2606    16658    contacto contacto_telefone_key 
   CONSTRAINT     ]   ALTER TABLE ONLY public.contacto
    ADD CONSTRAINT contacto_telefone_key UNIQUE (telefone);
 H   ALTER TABLE ONLY public.contacto DROP CONSTRAINT contacto_telefone_key;
       public                 postgres    false    223            u           2606    16664    empresa empresa_nif_key 
   CONSTRAINT     Q   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_nif_key UNIQUE (nif);
 A   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_nif_key;
       public                 postgres    false    227            w           2606    16666    empresa empresa_nif_key1 
   CONSTRAINT     R   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_nif_key1 UNIQUE (nif);
 B   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_nif_key1;
       public                 postgres    false    227            y           2606    16470    empresa empresa_pkey 
   CONSTRAINT     t   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_pkey PRIMARY KEY (identificador, cliente_identificador);
 >   ALTER TABLE ONLY public.empresa DROP CONSTRAINT empresa_pkey;
       public                 postgres    false    227    227            e           2606    16648    endereco endereco_pk 
   CONSTRAINT     ]   ALTER TABLE ONLY public.endereco
    ADD CONSTRAINT endereco_pk PRIMARY KEY (identificador);
 >   ALTER TABLE ONLY public.endereco DROP CONSTRAINT endereco_pk;
       public                 postgres    false    222            }           2606    16541    extrato extrato_pk 
   CONSTRAINT     [   ALTER TABLE ONLY public.extrato
    ADD CONSTRAINT extrato_pk PRIMARY KEY (identificador);
 <   ALTER TABLE ONLY public.extrato DROP CONSTRAINT extrato_pk;
       public                 postgres    false    229            c           2606    16550    levantamento levantamento_pk 
   CONSTRAINT     e   ALTER TABLE ONLY public.levantamento
    ADD CONSTRAINT levantamento_pk PRIMARY KEY (identificador);
 F   ALTER TABLE ONLY public.levantamento DROP CONSTRAINT levantamento_pk;
       public                 postgres    false    221            a           2606    16559    parcelado parcelado_pk 
   CONSTRAINT     _   ALTER TABLE ONLY public.parcelado
    ADD CONSTRAINT parcelado_pk PRIMARY KEY (identificador);
 @   ALTER TABLE ONLY public.parcelado DROP CONSTRAINT parcelado_pk;
       public                 postgres    false    219            q           2606    16668    particular particular_bi_key 
   CONSTRAINT     U   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT particular_bi_key UNIQUE (bi);
 F   ALTER TABLE ONLY public.particular DROP CONSTRAINT particular_bi_key;
       public                 postgres    false    226            s           2606    16561    particular particular_pk 
   CONSTRAINT     a   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT particular_pk PRIMARY KEY (identificador);
 B   ALTER TABLE ONLY public.particular DROP CONSTRAINT particular_pk;
       public                 postgres    false    226            _           2606    16532    transacao unique_transacao 
   CONSTRAINT     T   ALTER TABLE ONLY public.transacao
    ADD CONSTRAINT unique_transacao UNIQUE (pid);
 D   ALTER TABLE ONLY public.transacao DROP CONSTRAINT unique_transacao;
       public                 postgres    false    217                       2606    16495    empresa fk_empresa    FK CONSTRAINT     �   ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT fk_empresa FOREIGN KEY (cliente_identificador) REFERENCES public.cliente(identificador);
 <   ALTER TABLE ONLY public.empresa DROP CONSTRAINT fk_empresa;
       public               postgres    false    225    227    4719            ~           2606    16526    particular fk_particular    FK CONSTRAINT     �   ALTER TABLE ONLY public.particular
    ADD CONSTRAINT fk_particular FOREIGN KEY (cliente_identificador) REFERENCES public.cliente(identificador);
 B   ALTER TABLE ONLY public.particular DROP CONSTRAINT fk_particular;
       public               postgres    false    225    4719    226                  x������ � �            x������ � �         @   x��424�4732�LNLI,.)��4r9�tt��L��,�LL}9K8�b���� �(O         -   x�337H��404I4�H�424�4732���,�44������ ��r            x������ � �            x������ � �            x������ � �            x������ � �            x������ � �            x������ � �            x������ � �            x������ � �            x������ � �     